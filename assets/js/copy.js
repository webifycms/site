// ================================================================
// COPY TO CLIPBOARD
// ================================================================
// Gives share buttons (e.g. on blog posts) a one-click copy
// functionality using the Clipboard API.
//
// Usage in HTML:
//   <button class="share__btn--copy" data-copy-url="https://...">
//     Copy link
//   </button>
//
// On click:
//   1. Reads the URL from data-copy-url attribute
//   2. Copies it to the clipboard
//   3. Adds "is-copied" class for 2 seconds (used for visual
//      feedback, e.g. changing button text to "Copied!")
//
// Note: navigator.clipboard.writeText() requires a secure context
// (HTTPS or localhost). On HTTP the promise will reject silently.
// ================================================================

export function init() {
	document.querySelectorAll(".share__btn--copy").forEach(function (btn) {
		btn.addEventListener("click", function () {
			var url = btn.getAttribute("data-copy-url");

			if (!url) return;

			navigator.clipboard.writeText(url).then(function () {
				btn.classList.add("is-copied");

				setTimeout(function () {
					btn.classList.remove("is-copied");
				}, 2000);
			});
		});
	});
}
