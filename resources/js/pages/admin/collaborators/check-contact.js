import $ from 'jquery';

function dispatchCheckContactResult(form, detail) {
    if (!(form instanceof HTMLElement)) {
        return;
    }
    form.dispatchEvent(
        new CustomEvent('catalog-check-contact', {
            bubbles: true,
            detail: detail,
        })
    );
}

$(document).ready(function () {
    $('form[data-check-contact-route]').each(function () {
        const $form = $(this);
        const $emailInput = $form.find('input[name="email"]').first();
        if (!$emailInput.length) {
            return;
        }

        const checkContactRoute = $form.attr('data-check-contact-route');
        if (!checkContactRoute) {
            return;
        }

        const formEl = this;
        const $emailWarning = $form.find('#email-warning');
        const $emailSuccess = $form.find('#email-success');
        const $emailPending = $form.find('#email-pending-verification');
        const $emailInternal = $form.find('.js-email-internal-reserved');
        const $fullNameInput = $form.find('input[name="full_name"], #full_name').first();

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const KEYUP_DEBOUNCE_MS = 300;
        let keyupTimer = null;
        let checkContactXhr = null;

        const $nameDiffers = $form.find('.js-email-name-differs-warning');
        const $nameDiffersText = $form.find('.js-email-name-differs-warning-text');
        const nameDiffersMsg = $form.attr('data-msg-name-differs-warning') || '';
        const $networkError = $form.find('.js-email-check-contact-network-error');
        const $emailAdminDuplicate = $form.find('#email-admin-duplicate');
        const isAdminCollaboratorForm =
            $form.attr('data-admin-collaborator-form') === '1' || $form.attr('data-admin-collaborator-form') === 'true';
        const currentCollaboratorIdAttr = $form.attr('data-current-collaborator-id');
        const currentCollaboratorIdParsed = (() => {
            if (currentCollaboratorIdAttr === undefined || currentCollaboratorIdAttr === '') {
                return NaN;
            }
            const n = parseInt(String(currentCollaboratorIdAttr), 10);

            return Number.isNaN(n) ? NaN : n;
        })();

        function hideNetworkError() {
            if ($networkError.length) {
                $networkError.prop('hidden', true);
            }
        }

        function showNetworkError() {
            if ($networkError.length) {
                $networkError.prop('hidden', false);
            }
        }

        /**
         * When the email belongs to an existing external collaborator, fill full name from the server
         * unless the user already entered a different name (name_differs_from_record).
         */
        function applyFullNameFromCheckResponse(data, exists, nameDiffersFromRecord) {
            if (
                !$fullNameInput.length ||
                !data ||
                typeof data !== 'object' ||
                !exists ||
                nameDiffersFromRecord === true
            ) {
                return;
            }
            const raw = data.full_name;
            const name = typeof raw === 'string' ? raw.trim() : '';
            if (name === '') {
                return;
            }
            $fullNameInput.val(name);
            $fullNameInput.trigger('input').trigger('change');
        }

        function updateNameDiffersBanner(nameDiffersFromRecord) {
            if (!$nameDiffers.length) {
                return;
            }
            if (nameDiffersFromRecord === true && nameDiffersMsg !== '') {
                $nameDiffersText.text(nameDiffersMsg);
                $nameDiffers.prop('hidden', false);
            } else {
                $nameDiffers.prop('hidden', true);
            }
        }

        function hideInternal() {
            if ($emailInternal.length) {
                $emailInternal.prop('hidden', true);
            }
        }

        /**
         * @param {{ fillFullNameFromRecord?: boolean }} [options] When false (name field edits), only refresh banners / events — never overwrite full_name from the server.
         */
        function checkContact(options) {
            const fillFullNameFromRecord = options?.fillFullNameFromRecord !== false;
            const email = String($emailInput.val() || '').trim();

            if (!email) {
                hideNetworkError();
                $emailWarning.prop('hidden', true);
                $emailSuccess.prop('hidden', true);
                if ($emailPending.length) {
                    $emailPending.prop('hidden', true);
                }
                if ($emailAdminDuplicate.length) {
                    $emailAdminDuplicate.prop('hidden', true);
                }
                hideInternal();
                updateNameDiffersBanner(false);
                dispatchCheckContactResult(formEl, {
                    internalReserved: false,
                    exists: false,
                });
                return;
            }

            if (checkContactXhr) {
                checkContactXhr.abort();
            }

            hideNetworkError();

            const xhr = $.ajax({
                type: 'POST',
                url: checkContactRoute,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                data: {
                    _token: csrfToken,
                    email: email,
                    full_name: $fullNameInput.length ? String($fullNameInput.val() || '').trim() : '',
                },
                success: function (data) {
                    hideNetworkError();
                    const internalReserved = data && data.internal_reserved === true;
                    const exists = data && data.exists === true;
                    const sessionOk = data && data.contribution_session_verified === true;
                    const nameDiffersFromRecord = data && data.name_differs_from_record === true;

                    dispatchCheckContactResult(formEl, {
                        internalReserved,
                        exists,
                        sessionOk,
                        emailVerified: data && data.skip_contact_check !== true && data.email_verified === true,
                        nameDiffersFromRecord,
                    });

                    if (internalReserved) {
                        $emailWarning.prop('hidden', true);
                        $emailSuccess.prop('hidden', true);
                        if ($emailPending.length) {
                            $emailPending.prop('hidden', true);
                        }
                        if ($emailAdminDuplicate.length) {
                            $emailAdminDuplicate.prop('hidden', true);
                        }
                        if ($emailInternal.length) {
                            $emailInternal.prop('hidden', false);
                        }
                        if ($fullNameInput.length) {
                            $fullNameInput.val('');
                            $fullNameInput.trigger('input').trigger('change');
                        }
                        updateNameDiffersBanner(false);
                        return;
                    }

                    hideInternal();
                    updateNameDiffersBanner(nameDiffersFromRecord);

                    if (isAdminCollaboratorForm) {
                        $emailWarning.prop('hidden', true);
                        $emailSuccess.prop('hidden', true);
                        if ($emailPending.length) {
                            $emailPending.prop('hidden', true);
                        }
                        if (!exists) {
                            if ($emailAdminDuplicate.length) {
                                $emailAdminDuplicate.prop('hidden', true);
                            }
                            return;
                        }
                        const responseCollaboratorIdRaw = data && data.collaborator_id;
                        const responseCollaboratorId =
                            responseCollaboratorIdRaw != null ? parseInt(String(responseCollaboratorIdRaw), 10) : NaN;
                        const isOwnRecord =
                            !Number.isNaN(currentCollaboratorIdParsed) &&
                            !Number.isNaN(responseCollaboratorId) &&
                            currentCollaboratorIdParsed === responseCollaboratorId;
                        if (isOwnRecord) {
                            if ($emailAdminDuplicate.length) {
                                $emailAdminDuplicate.prop('hidden', true);
                            }
                            if (fillFullNameFromRecord) {
                                applyFullNameFromCheckResponse(data, exists, nameDiffersFromRecord);
                            }
                            return;
                        }
                        if ($emailAdminDuplicate.length) {
                            $emailAdminDuplicate.prop('hidden', false);
                        }
                        return;
                    }

                    if (!exists) {
                        $emailWarning.prop('hidden', false);
                        $emailSuccess.prop('hidden', true);
                        if ($emailPending.length) {
                            $emailPending.prop('hidden', true);
                        }
                    } else if ($emailPending.length && !sessionOk) {
                        const dbVerified = data && data.skip_contact_check !== true && data.email_verified === true;
                        $emailPending.find('.js-email-pending-first-time').prop('hidden', dbVerified);
                        $emailPending.find('.js-email-pending-session').prop('hidden', !dbVerified);
                        $emailWarning.prop('hidden', true);
                        $emailSuccess.prop('hidden', true);
                        $emailPending.prop('hidden', false);
                        if (fillFullNameFromRecord) {
                            applyFullNameFromCheckResponse(data, exists, nameDiffersFromRecord);
                        }
                    } else {
                        $emailWarning.prop('hidden', true);
                        $emailSuccess.prop('hidden', false);
                        if ($emailPending.length) {
                            $emailPending.prop('hidden', true);
                        }
                        if (fillFullNameFromRecord) {
                            applyFullNameFromCheckResponse(data, exists, nameDiffersFromRecord);
                        }
                    }
                },
                error: function (_jq, textStatus) {
                    if (textStatus === 'abort') {
                        return;
                    }
                    hideInternal();
                    updateNameDiffersBanner(false);
                    dispatchCheckContactResult(formEl, {
                        internalReserved: false,
                        exists: false,
                    });
                    $emailWarning.prop('hidden', true);
                    $emailSuccess.prop('hidden', true);
                    if ($emailPending.length) {
                        $emailPending.prop('hidden', true);
                    }
                    if ($emailAdminDuplicate.length) {
                        $emailAdminDuplicate.prop('hidden', true);
                    }
                    showNetworkError();
                },
                complete: function () {
                    if (checkContactXhr === xhr) {
                        checkContactXhr = null;
                    }
                },
            });
            checkContactXhr = xhr;
        }

        function scheduleCheckContactFromEmail() {
            if (keyupTimer !== null) {
                clearTimeout(keyupTimer);
            }
            keyupTimer = setTimeout(function () {
                keyupTimer = null;
                checkContact({ fillFullNameFromRecord: true });
            }, KEYUP_DEBOUNCE_MS);
        }

        function scheduleCheckContactFromNameOnly() {
            if (keyupTimer !== null) {
                clearTimeout(keyupTimer);
            }
            keyupTimer = setTimeout(function () {
                keyupTimer = null;
                checkContact({ fillFullNameFromRecord: false });
            }, KEYUP_DEBOUNCE_MS);
        }

        $emailInput.on('blur', function () {
            checkContact({ fillFullNameFromRecord: true });
        });

        const isExtraModal = $form.is('#addExtraForm');
        const isItemCreate = $form.is('#item-create-form');
        if (isExtraModal || isItemCreate) {
            $emailInput.on('change', function () {
                checkContact({ fillFullNameFromRecord: true });
            });
            $emailInput.on('keyup', scheduleCheckContactFromEmail);
            if ($fullNameInput.length) {
                $fullNameInput.on('input change', function () {
                    if (String($emailInput.val() || '').trim() !== '') {
                        scheduleCheckContactFromNameOnly();
                    }
                });
            }
        }

        $emailInput.on('input', function () {
            if ($(this).val() === '') {
                hideNetworkError();
                $emailWarning.prop('hidden', true);
                $emailSuccess.prop('hidden', true);
                if ($emailPending.length) {
                    $emailPending.prop('hidden', true);
                }
                if ($emailAdminDuplicate.length) {
                    $emailAdminDuplicate.prop('hidden', true);
                }
                hideInternal();
                updateNameDiffersBanner(false);
                dispatchCheckContactResult(formEl, {
                    internalReserved: false,
                    exists: false,
                });
            }
        });

        if (String($emailInput.val() || '').trim() !== '') {
            checkContact({ fillFullNameFromRecord: true });
        }
    });
});
