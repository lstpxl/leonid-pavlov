# TECH_README

Technical documentation for the `leonid-pavlov` site. For the project's public
description (in Russian), see [`README.md`](./README.md).

## Overview

A static website archiving the poems and restored audio recordings of Leonid
Semyonovich Pavlov. It is built with [Hugo](https://gohugo.io) and deployed to
GitHub Pages at <https://lstpxl.github.io/leonid-pavlov/>.

The site is content-driven: poems and recordings live as Markdown files under
`content/`, and Hugo renders them into a static `public/` directory.

## Requirements

| Tool | Version | Purpose |
| --- | --- | --- |
| [Hugo](https://gohugo.io) **extended** | `0.163.x` | Site build + SCSS (LibSass) + JS bundling (esbuild) |
| [Node.js](https://nodejs.org) | `>= 20` (tested on 24) | Runs the npm scripts and `stylelint` |
| npm | bundled with Node | Dev dependencies + scripts |

Notes:

- The **extended** edition of Hugo is required (it bundles the SCSS transpiler
  and WebP support). Verify with `hugo version` — it should contain `+extended`.
- No separate Dart Sass install is needed: SCSS is currently transpiled with the
  built-in **LibSass** engine (see _Known follow-ups_ below).
- Install Hugo via Homebrew (`brew install hugo`), or pin a specific version from
  the [Hugo releases](https://github.com/gohugoio/hugo/releases).

## Getting started

```bash
npm install        # install dev dependencies (stylelint et al.)
npm run dev        # start the local dev server at http://localhost:1313/
```

## npm scripts

| Script | Command | Description |
| --- | --- | --- |
| `npm run dev` | `hugo server` | Local dev server with live reload |
| `npm run dev:drafts` | `hugo server --buildDrafts --buildFuture` | Dev server including drafts/future-dated content |
| `npm run build` | `hugo --gc --minify` | Production-style build into `public/` |
| `npm run build:prod` | `hugo --gc --minify --environment production` | Build with the `production` environment |
| `npm run preview` | `hugo server --environment production` | Preview the production build locally |
| `npm run clean` | `rm -rf public resources` | Remove generated output and resource cache |
| `npm run lint` | `stylelint "assets/scss/**/*.scss"` | Lint SCSS |
| `npm run lint:fix` | `stylelint … --fix` | Auto-fix SCSS lint issues |

## Project structure

```
content/            Markdown content (poems, recordings, bio/preface) — the data
  verses/           Poems (one file per poem)
  recordings/       Audio recording pages (front matter points to a .aac file)
  top10.md          Curated "top 10" selection page
  _index.md         Home page (uses the `author` layout)
layouts/            Hugo templates (new v0.146+ template system)
  baseof.html       Base template
  author.html       Home page layout (custom `layout: author`)
  top10.html        Custom `layout: top10`
  empty.html        Intentionally blank layout for non-listed pages
  verses/           Section templates for /verses
  recordings/       Section templates for /recordings
  _partials/        Reusable partials (header, footer, menu, icons, lists, …)
assets/
  scss/             Source SCSS (entry point: main.scss)
  js/               main.js + vendored vanilla-lazyload
static/             Files copied verbatim (audio-files, images, favicon, robots)
archetypes/         Front-matter templates for `hugo new`
hugo.yaml           Site configuration
.github/workflows/  CI: builds with Hugo and deploys to GitHub Pages
```

## Configuration highlights (`hugo.yaml`)

- `locale: "ru"` — site language (RFC 5646 tag).
- `permalinks.recordings: "/recordings/:contentbasename/"` — recording URLs.
- `disableKinds` — taxonomies, RSS, 404, robots.txt, etc. are disabled (the
  site is a curated archive, not a blog).
- `markup.goldmark` — hard wraps enabled, footnotes + typographer on,
  raw inline HTML disabled (`unsafe: false`).

## Deployment

Pushing to `main` triggers `.github/workflows/hugo.yml`, which installs the
pinned Hugo version (`HUGO_VERSION`), builds with `--gc --minify`, and publishes
`public/` to GitHub Pages. Keep `HUGO_VERSION` in the workflow in sync with the
version used locally.

## Known follow-ups

- **LibSass is deprecated.** `layouts/_partials/style-link.html` uses
  `transpiler: libsass`, which Hugo deprecated in v0.153.0 and will remove in a
  future release. Migrating to Dart Sass (`transpiler: dartsass`) requires
  installing the Dart Sass binary (`brew install sass/sass/sass` locally; the CI
  workflow already installs it via snap).
- **Stylelint** reports auto-fixable issues confined to the Google-generated
  `assets/scss/google-fonts.scss`; run `npm run lint:fix` to clear them.
