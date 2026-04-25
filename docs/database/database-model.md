# Database Model (ER Diagram)

This diagram is generated from the current Laravel migrations under `database/migrations/`.

```mermaid
erDiagram
    LANGUAGES {
        bigint id PK
        string code UK
        string name
    }

    ADMINS {
        bigint id PK
        string username
        string password
    }

    COLLABORATORS {
        bigint id PK
        string full_name
        string email UK
        enum role
        bool blocked
        timestamp last_email_verification_at
    }

    LOCATIONS {
        bigint id PK
        string name
        string code UK
    }

    ITEM_CATEGORIES {
        bigint id PK
    }

    ITEM_CATEGORY_TRANSLATIONS {
        bigint id PK
        bigint item_category_id FK
        bigint language_id FK
        string name
    }

    TAG_CATEGORIES {
        bigint id PK
    }

    TAG_CATEGORY_TRANSLATIONS {
        bigint id PK
        bigint tag_category_id FK
        bigint language_id FK
        string name
    }

    ITEMS {
        bigint id PK
        date date
        string identification_code
        bool validation
        bigint category_id FK
        bigint collaborator_id FK
        bigint location_id FK
    }

    ITEM_TRANSLATIONS {
        bigint id PK
        bigint item_id FK
        bigint language_id FK
        string name
        text description
        text history
        text detail
    }

    ITEM_IMAGES {
        bigint id PK
        bigint item_id FK
        text path
        enum type
        int sort_order
    }

    TAGS {
        bigint id PK
        bool validation
        bigint tag_category_id FK
    }

    TAG_TRANSLATIONS {
        bigint id PK
        bigint tag_id FK
        bigint language_id FK
        text name
    }

    ITEM_TAG {
        bigint id PK
        bigint item_id FK
        bigint tag_id FK
        bool validation
    }

    EXTRAS {
        bigint id PK
        bigint item_id FK
        bigint collaborator_id FK
        bool validation
    }

    EXTRA_TRANSLATIONS {
        bigint id PK
        bigint extra_id FK
        bigint language_id FK
        text info
    }

    ITEM_COMPONENT {
        bigint id PK
        bigint item_id FK
        bigint component_id FK
        bool validation
    }

    LOCKS {
        bigint id PK
        string lockable_type
        bigint lockable_id
        bigint admin_id FK
        timestamp expiry_date
    }

    LANGUAGES ||--o{ ITEM_CATEGORY_TRANSLATIONS : translates
    ITEM_CATEGORIES ||--o{ ITEM_CATEGORY_TRANSLATIONS : has

    LANGUAGES ||--o{ TAG_CATEGORY_TRANSLATIONS : translates
    TAG_CATEGORIES ||--o{ TAG_CATEGORY_TRANSLATIONS : has

    LOCATIONS ||--o{ ITEMS : places
    ITEM_CATEGORIES ||--o{ ITEMS : categorizes
    COLLABORATORS ||--o{ ITEMS : contributes

    LANGUAGES ||--o{ ITEM_TRANSLATIONS : translates
    ITEMS ||--o{ ITEM_TRANSLATIONS : has

    ITEMS ||--o{ ITEM_IMAGES : has

    TAG_CATEGORIES ||--o{ TAGS : groups
    LANGUAGES ||--o{ TAG_TRANSLATIONS : translates
    TAGS ||--o{ TAG_TRANSLATIONS : has

    ITEMS ||--o{ ITEM_TAG : tagged
    TAGS ||--o{ ITEM_TAG : tags

    ITEMS ||--o{ EXTRAS : has
    COLLABORATORS ||--o{ EXTRAS : contributes
    LANGUAGES ||--o{ EXTRA_TRANSLATIONS : translates
    EXTRAS ||--o{ EXTRA_TRANSLATIONS : has

    ITEMS ||--o{ ITEM_COMPONENT : parent_item
    ITEMS ||--o{ ITEM_COMPONENT : component_item

    ADMINS ||--o{ LOCKS : owns
```

## Notes

- Translation tables enforce uniqueness per `(entity_id, language_id)`.
- `locks` uses a polymorphic relation (`lockable_type`, `lockable_id`).
- `item_component` is a self-relation on `items` (`item_id` -> `component_id`).
- Some foreign keys are nullable with `nullOnDelete()`, following migration rules.
