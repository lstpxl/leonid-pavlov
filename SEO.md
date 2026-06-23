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

### Pending / watch

- Sitemap shows **"Couldn't fetch"** >5 min after re-submit. This is a known
  **stale/delayed GSC status**, not a real failure: the sitemap returns `200`
  `application/xml`, valid XML, and host `robots.txt` allows all (verified as
  Googlebot). Do **not** keep re-submitting. Expect it to flip to Success within
  ~24–48h. Indexing of URL-Inspection-requested pages proceeds regardless.

## TODO

GSC:

- [ ] Re-check sitemap status in 24–48h (should be Success, ~242 pages).
- [ ] URL Inspection → Request Indexing for `/` and a few strong poems
      (e.g. `…/verses/p03/`); rate-limited ~10–15/day, sitemap covers the rest.
- [ ] After a few days: check **Pages** report; note the dominant
      "not indexed" reason and act on it.

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
