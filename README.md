# Approval

Package to be used for approval cycles

# How to install?

- Add These Lines to composer.json [repositories]
  {
  "type": "vcs",
  "url": "git@github.com:MahmouedMohamed/Approval.git"
  }
- Add This Keys to auth.json [Not Needed if you found it public :)]
  "github-oauth": {
  "github.com": XXX_XXX_XXX
  }
- Run composer require mahmoued/approval
- Add ApprovalServiceProvider::class, to app.php [providers, aliases]
- Run command php artisan vendor:publish --tag=migrations
- Run Migration using php artisan migrate or use php artisan migrate --path=\database\migrations\2023_03_29_155454_create_reviews_table.php
- Add Approvable Trait to any model you want
- Define your model's variables
  -- REQUIRED_NUMBER_OF_APPROVALS
  -- APPROVED_STATUS
  -- PENDING_APPROVAL_STATUS
  -- PENDING_APPROVAL_STATUS_TEXT
  -- REJECTED_STATUS
  -- REJECTED_STATUS_TEXT
- Define your model's status converter model variables
  -- latestIndex (indicate latest value in $values array)
  -- approvalIndexes (indicates Entries for Approval Cycle)
