# Project Rules for Pressmatik

## Image Optimization Policy

1. **Static Assets (`public/images/`)**:
   - Optimizations and conversions for files in `public/images/` MUST be done **locally** and committed to Git.
   - Never run any command on the live server that modifies files in `public/images/`.

2. **Dynamic Uploads (`public/uploads/`)**:
   - Never commit files under `public/uploads/` to Git (enforced via `.gitignore`).
   - Image optimization for uploads (`app:optimize-images`) runs **only on the live server** via `build.sh`.
