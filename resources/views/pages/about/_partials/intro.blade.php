<h1>{{ __('view.about.heading') }}</h1>
<div class="row">
    <div class="col-12">
        <div class="about-lede">
            <p class="about-lede__intro mb-3">{{ __('view.about.intro') }}</p>
            <p class="about-lede__credits mb-0">
                <span class="about-lede__credits-lead">{{ __('view.about.credits_lead') }}</span>
                {{ __('view.about.credits_p1_before_author') }}
                <strong>{{ __('view.about.credits_author_original') }}</strong>
                (<strong>{{ __('view.about.credits_year_original') }}</strong>) —
                <a class="about-inline-link" target="_blank" rel="noopener noreferrer"
                    href="{{ __('view.about.credits_repo_original_href') }}">{{ __('view.about.credits_link_original_label') }}</a>.
                {{ __('view.about.credits_p2') }}
                <strong>{{ __('view.about.credits_year_current') }}</strong>
                {{ __('view.about.credits_p3') }}
                <strong>{{ __('view.about.credits_author_current') }}</strong>
                —
                <a class="about-inline-link" target="_blank" rel="noopener noreferrer"
                    href="{{ __('view.about.credits_repo_current_href') }}">{{ __('view.about.credits_link_current_label') }}</a>.
            </p>
            <p class="about-lede__contact mb-0">
                {{ __('view.about.contact_intro') }}
                <a class="about-inline-link"
                    href="mailto:{{ e(__('view.about.contact_email_general')) }}">{{ __('view.about.contact_email_general') }}</a>
                {{ __('view.about.contact_joiner') }}
                <a class="about-inline-link"
                    href="mailto:{{ e(__('view.about.contact_email_institutional')) }}">{{ __('view.about.contact_email_institutional') }}</a>
            </p>
        </div>
    </div>
</div>
