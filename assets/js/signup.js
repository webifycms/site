// ================================================================
// NEWSLETTER SIGNUP FORM
// ================================================================
// Handles client-side validation and submission of the newsletter
// signup form to POST /api/subscribe.
//
// Required HTML elements (all inside #signupForm):
//   #sName, #sEmail, #sConsent   — input fields
//   #nameErr, #emailErr, #consentErr — error message containers
//   #signupSuccess               — success message (shown after submit)
//
// Validation rules:
//   - Name:    required (non-empty after trim)
//   - Email:   required, must contain @ and .
//   - Consent: checkbox must be checked
//
// API response handling:
//   - 200 + { success: true }  → show success
//   - 409 (Conflict)           → already subscribed, show success
//   - 422 + { errors: {...} }  → show field-level errors from server
//   - Other / network error    → generic alert
//
// CSS classes toggled:
//   .--error  on input   — red border highlight
//   .--show   on <span>  — error message visible
// ================================================================

export function init() {
	var form = document.getElementById("signupForm");
	if (!form) return;

	// --- DOM references ---
	var nIn = document.getElementById("sName");
	var eIn = document.getElementById("sEmail");
	var cIn = document.getElementById("sConsent");
	var nErr = document.getElementById("nameErr");
	var eErr = document.getElementById("emailErr");
	var cErr = document.getElementById("consentErr");
	var succ = document.getElementById("signupSuccess");

	// Toggle error state on a single field + its error message
	function err(inp, el, show) {
		inp.classList.toggle("--error", show);
		el.classList.toggle("--show", show);
	}

	// Reset all field errors and clear message text
	function clearFieldErrors() {
		err(nIn, nErr, false);
		err(eIn, eErr, false);
		err(cIn, cErr, false);
		nErr.textContent = "";
		eErr.textContent = "";
		cErr.textContent = "";
	}

	// Toggle submit button text and disabled state during fetch
	function setSubmitting(submitting, btn, originalText) {
		btn.textContent = submitting ? "Subscribing..." : originalText;
		btn.disabled = submitting;
	}

	// Hide form and show the success message block
	function showSuccess() {
		form.style.display = "none";
		succ.style.display = "block";
	}

	// Display server-returned validation errors next to each field.
	// The API returns errors as { fieldName: ["message"] } — we
	// take the first message from each array.
	function showFieldErrors(errors) {
		clearFieldErrors();

		if (errors.name) {
			nIn.classList.add("--error");
			nErr.textContent = errors.name[0];
			nErr.classList.add("--show");
		}
		if (errors.email) {
			eIn.classList.add("--error");
			eErr.textContent = errors.email[0];
			eErr.classList.add("--show");
		}
		if (errors.consent) {
			cIn.classList.add("--error");
			cErr.textContent = errors.consent[0];
			cErr.classList.add("--show");
		}
	}

	// --- Form submission ---
	form.addEventListener("submit", function (e) {
		e.preventDefault();
		clearFieldErrors();

		var nv = nIn.value.trim();
		var ev = eIn.value.trim();
		var ok = true;

		// Client-side validation
		if (!nv) {
			err(nIn, nErr, true);
			ok = false;
		}
		if (!ev || !ev.includes("@") || !ev.includes(".")) {
			err(eIn, eErr, true);
			ok = false;
		}
		if (!cIn.checked) {
			err(cIn, cErr, true);
			ok = false;
		}
		if (!ok) return;

		var btn = form.querySelector('button[type="submit"]');
		var originalText = btn.textContent;

		setSubmitting(true, btn, originalText);

		// POST to the subscribe API endpoint
		fetch("/api/subscribe", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: JSON.stringify({ name: nv, email: ev }),
		})
			.then(function (res) {
				return res.json().then(function (data) {
					return { ok: res.ok, status: res.status, data: data };
				});
			})
			.then(function (result) {
				var data = result.data;

				// New subscriber — success
				if (result.ok && data.success) {
					showSuccess();
					return;
				}

				// Already subscribed — treat as success
				if (409 === result.status) {
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
	// or changes the checkbox, so errors don't linger after correction.
	nIn.addEventListener("input", function () {
		err(nIn, nErr, false);
	});
	eIn.addEventListener("input", function () {
		err(eIn, eErr, false);
	});
	cIn.addEventListener("change", function () {
		err(cIn, cErr, false);
	});
}
