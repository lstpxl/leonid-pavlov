# SEO notes

Status of search-indexing work for <https://lstpxl.github.io/leonid-pavlov/>.

## Background

For ~1 year the site had **0 pages indexed** by Google. Root cause was a
**discovery failure**, not a quality penalty:

- The GSC sitemap was submitted on the wrong property and pointed at the host
  root sitemap (`Couldn't fetch`, 0 pages). The real 242-URL sitemap at
  `…/leonid-pavlov/sitemap.xml` was never submitted.
- `robots.txt` is only read from the **host root** (`lstpxl.github.io/robots.txt`),
  which is served by the **`lstpxl.github.io`** user-site repo — not by this repo
  and not by the unrelated `lstpxl` project repo (which publishes to the dead
  `/lstpxl/` path).
- Content pages were thin / high-boilerplate (esp. recordings had ~62 chars of
  unique text), a secondary signal.

## Done

On-page (this repo, committed + live):

- Per-page `<meta name="description">` (`layouts/_partials/meta-description.html`):
  poem opening lines for verses, generated line for recordings, site default
  elsewhere.
- JSON-LD structured data (`layouts/_partials/schema.html`): `Person` (home),
  `CreativeWork` w/ full poem text (verses), `AudioObject` w/ ISO-8601 duration
  (recordings).
- Recording pages show the matching poem in a collapsed `<details>`
  (`layouts/recordings/single.html`): matched by exact title, or by explicit
  `verse = '<id>'` front-matter override. 22/49 auto-matched today.
- Sitemap `<lastmod>` via `enableGitInfo: true` (`hugo.yaml`) + `fetch-depth: 0`
  in CI (`.github/workflows/hugo.yml`). Live sitemap: 242 loc / 242 lastmod.

Host root (`lstpxl.github.io` repo, deployed + live):

- `robots.txt` → allow all + `Sitemap: https://lstpxl.github.io/sitemap.xml`.
- `sitemap.xml` → sitemap **index** referencing `…/leonid-pavlov/sitemap.xml`.
- `index.html` → hub landing page (root no longer 404).

## GSC done (2026-06-23)

- [x] Added URL-prefix property `https://lstpxl.github.io/leonid-pavlov/`.
- [x] Submitted sitemap `sitemap.xml` in that property.
- [x] Requested Indexing for `/top10/`.
- [x] Added root property `https://lstpxl.github.io/` + submitted sitemap index.

### GSC sitemap status (2026-06-29, +1 week)

Still **"Sitemap could not be read"**, 0 discovered pages — but the file itself is
**not broken** (verified live):

- `https://lstpxl.github.io/leonid-pavlov/sitemap.xml` → `200`,
  `content-type: application/xml`, ~28 KB, valid XML, 242 `<loc>`, 242
  `<lastmod>`, all URLs under the property prefix, no duplicates.
- Host `robots.txt` allows all; verification file returns `200`.
- Conclusion: **GSC reporting / fetch quirk**, not a site bug. Indexing can still
  proceed via URL Inspection and internal links; sitemap is an accelerator.

#### If GSC still fails after re-submit

1. **URL Inspection** on the sitemap URL itself
   (`https://lstpxl.github.io/leonid-pavlov/sitemap.xml`) → **Test live URL**.
   Read the exact error Google reports (this is more reliable than the Sitemaps
   summary).
2. **Remove** the failed `/sitemap.xml` entry from Sitemaps (don't keep
   re-submitting the same row — it can preserve a stale failure).
3. **Re-add using the full absolute URL** (not just `sitemap.xml`):
   `https://lstpxl.github.io/leonid-pavlov/sitemap.xml`
4. Confirm the property is exactly **`https://lstpxl.github.io/leonid-pavlov/`**
   (URL-prefix, trailing slash).
5. Check the **root** property sitemap index (`https://lstpxl.github.io/sitemap.xml`)
   — if that one shows Success, discovery still works via the index chain.
6. **Don't block on sitemap Success** — keep Request Indexing for `/`, `/top10/`,
   and key poems; add 1–2 backlinks (biggest lever for a new `github.io` site).

## TODO

GSC:

- [ ] URL Inspection → **Test live URL** on `…/leonid-pavlov/sitemap.xml`; note
      the exact error text.
- [ ] Delete failed sitemap row → re-add as full URL
      `https://lstpxl.github.io/leonid-pavlov/sitemap.xml`.
- [ ] URL Inspection → Request Indexing for `/` and a few strong poems
      (e.g. `…/verses/p03/`); rate-limited ~10–15/day.
- [ ] Check **Pages** report; note the dominant "not indexed" reason.
- [ ] (Optional) Add `static/.nojekyll` + pretty-printed sitemap template if live
      test shows a parse/HTML issue (unlikely given current checks).

Cleanups:

- [x] Removed the dead `/another-project/` link from the `lstpxl.github.io`
      `index.html`.
- [ ] Map the remaining ~27 recordings to their poems (add `verse = '<id>'` to
      their front matter) so those pages get real text. Variant titles / "(2)"
      suffixes / fragments need manual review.
- [ ] (Optional) Drop the unused `lstpxl` project repo (serves dead `/lstpxl/`).
- [ ] (Optional) Remove `static/robots.txt` from this repo — it is ignored by
      crawlers (only the host-root one counts).

Longer-term (biggest lever for a new, niche, free-subdomain site):

- [ ] Get a few real **backlinks** (family/personal site, WWII/Leningrad-blockade
      or poetry communities, VK/Telegram, Wikidata if notable). This is usually
      what flips "discovered/crawled – currently not indexed" to indexed.
- [ ] (Optional) Custom domain — crawled more readily than a deep `github.io`
      subpath and allows a verifiable Domain property.
