// ================================================================
// NEWSLETTER UNSUBSCRIBE / DATA DELETION FORM
// ================================================================
// Handles client-side validation and submission of the data
// deletion request form to POST /api/unsubscribe.
//
// Required HTML elements (all inside #unsubscribeForm):
//   #uEmail              — email input field
//   #emailErr            — error message container
//   #unsubscribeSuccess  — success message (shown after submit)
//
// Validation rules:
//   - Email: required, must contain @ and .
//
// API response handling:
//   - 200 + { success: true }  → show success
//   - 422 + { errors: {...} }  → show field-level errors from server
//   - Other / network error    → generic alert
//
// CSS classes toggled:
//   .--error  on input   — red border highlight
//   .--show   on <span>  — error message visible
// ================================================================

export function init() {
	var form = document.getElementById("unsubscribeForm");
	if (!form) return;

	// --- DOM references ---
	var eIn = document.getElementById("uEmail");
	var eErr = document.getElementById("emailErr");
	var succ = document.getElementById("unsubscribeSuccess");

	// Toggle error state on a single field + its error message
	function err(inp, el, show) {
		inp.classList.toggle("--error", show);
		el.classList.toggle("--show", show);
	}

	// Reset all field errors and clear message text
	function clearFieldErrors() {
		err(eIn, eErr, false);
		eErr.textContent = "";
	}

	// Toggle submit button text and disabled state during fetch
	function setSubmitting(submitting, btn, originalText) {
		btn.textContent = submitting ? "Deleting..." : originalText;
		btn.disabled = submitting;
	}

	// Hide form and show the success message block
	function showSuccess() {
		form.style.display = "none";
		succ.style.display = "block";
	}

	// Display server-returned validation errors next to each field.
	function showFieldErrors(errors) {
		clearFieldErrors();

		if (errors.email) {
			eIn.classList.add("--error");
			eErr.textContent = errors.email[0];
			eErr.classList.add("--show");
		}
	}

	// --- Form submission ---
	form.addEventListener("submit", function (e) {
		e.preventDefault();
		clearFieldErrors();

		var ev = eIn.value.trim();
		var ok = true;

		// Client-side validation
		if (!ev || !ev.includes("@") || !ev.includes(".")) {
			err(eIn, eErr, true);
			ok = false;
		}
		if (!ok) return;

		var btn = form.querySelector('button[type="submit"]');
		var originalText = btn.textContent;

		setSubmitting(true, btn, originalText);

		// POST to the unsubscribe API endpoint
		fetch("/api/unsubscribe", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: JSON.stringify({ email: ev }),
		})
			.then(function (res) {
				return res.json().then(function (data) {
					return { ok: res.ok, status: res.status, data: data };
				});
			})
			.then(function (result) {
				var data = result.data;

				// Success (includes case where contact was not found - treated as success)
				if (result.ok && data.success) {
					showSuccess();
					return;
				}

				// Server-side validation failed — show field errors
				if (422 === result.status && data.errors) {
					showFieldErrors(data.errors);
					setSubmitting(false, btn, originalText);
					return;
				}

				// Unexpected response — generic error
				setSubmitting(false, btn, originalText);
				alert(data.message || "Something went wrong. Please try again later.");
			})
			.catch(function () {
				// Network error or JSON parse failure
				setSubmitting(false, btn, originalText);
				alert("Something went wrong. Please try again later.");
			});
	});

	// --- Inline error clearing ---
	// Clear a field's error state as soon as the user starts typing
	eIn.addEventListener("input", function () {
		err(eIn, eErr, false);
	});
}