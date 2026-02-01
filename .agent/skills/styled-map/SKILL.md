---
name: swell-styled-map-skill
description: SWELL Styled Google Mapプラグインの開発、保守、およびカスタマイズを行うためのスキル。日本語で対応してください。
---

# SWELL Styled Map 開発スキル

このスキルは、Googleマップをオシャレに埋め込むWordPressプラグイン「SWELL Styled Google Map」の保守と開発をガイドします。

## プロジェクト概要
- **目的**: Googleマップの標準iframeをラップし、CSSフィルター（グレースケール、反転）やカラーオーバーレイを適用して、サイトのデザインに馴染む地図を提供します。
- **主要技術**: PHP (WordPress), JavaScript (React/Gutenberg), CSS (Vanilla)

## ファイル構造
- `swell-styled-map.php`: プラグインのメインファイル。ショートコード、管理画面設定、ブロック登録を管理。
- `assets/js/block.js`: Gutenbergブロックの定義。React/Elementベース。
- `assets/js/frontend.js`: フロントエンドでのフェードイン効果などの制御。
- `build.ps1`: リリース用ZIP作成スクリプト（PowerShell）。バージョン自動取得・不要ファイル除外・ディレクトリ構造生成を行います。

## ソースコード管理
- **リポジトリ**: https://github.com/kazukiqq/swell-styled-map
- **ブランチ**: `main`
- **運用ルール**: 変更後は必ずコミットし、リモートへプッシュしてください。

```bash
git add .
git commit -m "変更内容の概要"
git push origin main
```

## 主要機能と仕様

### 1. ショートコード `[swell_styled_map]`
| 属性 | デフォルト | 内容 |
| :--- | :--- | :--- |
| `src` | (空) | Googleマップのiframe埋め込みURL |
| `color` | `#0091ff` | オーバーレイで使用するアクセントカラー |
| `height` | `600` | 地図の高さ (数値はpx換算、CSS単位も可) |
| `full_width` | `yes` | `yes`で全幅表示に拡張 |
| `invert` | `no` | `yes`でグレースケール＆階調反転（黒色ベース） |
| `effect` | `yes` | `yes`でスクロール時にフェードイン |

### 2. Gutenbergブロック
- カテゴリ: `swell`
- サーバーサイドレンダリング (`render_callback`) を使用。

## 開発ガイドライン

### 機能追加時の注意
1. **シンプルさを維持**: 複雑な地図APIは使用せず、標準の `iframe` 埋め込みをベースにすること。
2. **デザイン性**: SWELLテーマなどの高品質なサイトに馴染むプレミアムな質感を維持すること。
3. **セキュリティ**: URLの出力には `esc_url()`、クラス名には `esc_attr()` を使用すること。

### ZIPパッケージ化
リリース用のZIPファイル作成には、同梱のPowerShellスクリプト (`build.ps1`) を使用します。

このスクリプトは内部で `tar` コマンドを使用しており、ディレクトリ構造（ルートディレクトリ `swell-styled-map/` の包含）や権限設定を正しく維持したまま、安全にZIPファイルを作成します。従来の `Compress-Archive` で発生していた「ファイルをコピーできませんでした」エラーを回避できます。

```powershell
./build.ps1
```

※ `Compress-Archive` コマンドを直接実行したり、エクスプローラーの「送る -> 圧縮フォルダー」を使用しないでください。必ず `build.ps1` を経由してください。
