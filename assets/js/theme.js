// ================================================================
// DARK / LIGHT THEME TOGGLE
// ================================================================
// Toggles the data-theme attribute on <html> between "dark" and
// "light". The preference is persisted in localStorage under the
// key "wf-theme" so it survives page reloads.
//
// The initial theme is set server-side in header.php by reading
// localStorage before paint (inline <script> in <head>) to avoid
// a flash of wrong theme (FOWT).
//
// Related CSS: all color tokens in :root switch via
//   html[data-theme="light"] { ... }
// ================================================================

export function init() {
	const btn = document.getElementById("themeToggle");
	const html = document.documentElement;

	if (btn) {
		btn.addEventListener("click", function () {
			const next = html.getAttribute("data-theme") === "dark" ? "light" : "dark";
			html.setAttribute("data-theme", next);
			localStorage.setItem("wf-theme", next);
		});
	}
}
