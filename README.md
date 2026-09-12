# Sytvil Link Preview

A lightweight, privacy-conscious link preview module for HumHub. It automatically turns URLs posted in HumHub rich-text content into cached preview cards using metadata supplied by the linked website.

## Features

- Automatic previews for links in posts and comments
- Configurable 1–5 previews per rendered item
- Open Graph, Twitter Card and standard HTML metadata fallbacks
- Article metadata: author, publication date, section, locale and image dimensions
- Provider-aware presentation for YouTube, Vimeo and GitHub
- Favicon fallback when no preview image is available
- Optional local caching/proxying of preview images and favicons
- Canonical URL handling and tracking-parameter cleanup (`utm_*`, `fbclid`, `gclid`, etc.)
- Preview refresh and per-browser hide controls
- Visible broken-preview states with retry support
- Per-domain allow/block, image-disable and cache-lifetime rules
- Per-domain request limiting
- Admin cache dashboard with individual deletion and full purge
- Dark-theme-aware styling and compact mobile layout
- SSRF protections, redirect validation, request/body/time limits and libxml network blocking
- No cron job or background worker required

## Requirements

- HumHub 1.18.2 or newer
- PHP extensions normally required by HumHub, including cURL and DOM/libxml support

## Installation

1. Download or clone this repository.
2. Place the module directory in HumHub's `protected/modules/` directory using the folder name `sytvillinkpreview`.
3. Open **Administration → Modules** and enable **Sytvil Link Preview**.
4. Apply pending database migrations under **Administration → Information → Database**.
5. Flush HumHub caches if needed.

For a fresh install, both module migrations are applied in sequence. When upgrading from v1.0.0 to v2.0.0, apply:

```text
m260912_130000_link_preview_v2
```

## Configuration

The module administration screen allows you to configure preview count, cache lifetime, local image caching, mobile presentation, domain policies and rate limits.

Recommended shared-hosting defaults:

```text
Maximum previews: 2
Default cache lifetime: 7 days
Per-domain fetch limit: 20/hour
Cache images locally: ON
Compact mobile mode: ON
```

## How previews are generated

The module extracts normal HTTP/HTTPS links from HumHub rich text and fetches metadata only on a cache miss or explicit refresh. Metadata is taken from the linked website, preferring Open Graph and Twitter Card data and falling back to ordinary HTML metadata.

When local image caching is enabled, remote preview images and favicons can be cached under the HumHub uploads area and served from the local installation instead of requiring every visitor to contact the third-party host.

## Security

Because server-side link previews can create SSRF risk, the fetcher rejects localhost and private/reserved network destinations, validates redirects, only permits HTTP/HTTPS, limits redirects and response sizes, uses connection/request timeouts, and disables libxml network access while parsing HTML.

Please report security issues privately rather than opening a public issue. See [SECURITY.md](SECURITY.md).

## Shared-hosting design

Sytvil Link Preview does not require a worker or cron process. Fetches happen on cache misses or explicit refreshes, while metadata and optional image assets are cached to reduce repeated outbound requests.

## License

MIT License. See [LICENSE](LICENSE).

## Author

Sydney Chako
