---
title: 'WebifyCMS User Extension Update - Domain Complete, Infrastructure Next'
slug: webifycms-user-extension-update-domain-complete-infrastructure-next
date: '2026-08-05'
---

# WebifyCMS User Extension Update — The Domain Is Ready

Since the [first alpha release](/publishing/webifycms-first-alpha-release-the-bedrock), we have been hard at work
on the three pieces that unlock the first beta: the `ext-user` extension, the slot-based admin panel
`ext-admin`, and the official default theme `theme-canvas`. Today we want to share where the
User extension stands.

## What is the User Extension?

`ext-user` is the WebifyCMS extension that represents the **User bounded context**. It manages everything
user-related, including identity, authentication, and authorization — user management, profile and account
management, login, and registration.

It is not a standalone application. It is an extension of WebifyCMS that plugs into the `app` — building on
the shared kernel (`ext-base`) and contributing its own providers, routes, and console commands to the
running application.

## Current Status

The extension is under active development at version `0.2.0-beta`, and the domain layer is now essentially
complete. The work is organized around three bounded contexts inside the extension:

### Identity

The `Identity` context models the user itself — who a user is. It delivers the `User` entity along with
services for registration, email updates, and password changes. Value objects such as `UserEmail`,
`DisplayName`, `PasswordHash`, and `UserStatus` keep the model expressive and immutable. A `PasswordPolicy`
enforces strong password rules before any password is ever accepted.

### Authentication

The `Authentication` context answers the question "who is this person right now?" It introduces the `Session`
and `Challenge` entities, backed by a full set of domain services — `IssueChallenge`, `VerifyChallenge`,
`OpenSession`, `RefreshSession`, and `RevokeSession`. Challenges are generated through pluggable strategies
(code or token), and sessions carry their own access and refresh tokens.

### Authorization

The `Authorization` context answers "what are they allowed to do?" It adds the `Role` and `RoleAssignment`
entities, with services such as `CreateRole`, `AssignRole`, `RevokeRole`, and `CheckAccess`. A central
`Authorization` service ties permission checking together, and every role assignment is scoped by both a
tenant and a subject.

Every entity, service, guard, strategy, policy, and value object ships with a comprehensive unit test suite,
and dedicated architecture specifications enforce the Clean Architecture boundaries so the domain never
leaks into infrastructure. This is the same discipline that produced the solid `ext-base` foundation.

## What is Next

With the domain complete, the next phase is the **infrastructure layer** — the concrete persistence,
HTTP, and dependency-injection wiring that turns the domain model into a running extension. This means:

- Concrete repository implementations behind the repository interfaces
- Database schemas and migrations
- HTTP endpoints (login, registration, profiles) with the associated controllers and routes
- DI container providers that register the extension with `ext-base`

This is where the User extension's infrastructure work starts together with the **Admin extension** and the
**master theme Canvas**. The user experience spans both the admin panel and the front-end, so the
infrastructure cannot be built in isolation:

- `ext-admin` provides the slot-based admin panel where administrators manage users, roles, and permissions.
- `theme-canvas` is the official default theme that renders the front-end — login, registration,
  profile, and account pages.

The three are prepared and moving forward hand in hand. Once the infrastructure lands and these pieces
click together, we will be well on our way to the first beta release.

## How to Get Involved

WebifyCMS is fully open source under the MIT license. The User extension is available on GitHub, and we
welcome contributors, architects, and open-source enthusiasts to take a look, open issues, and share feedback.

- **GitHub:** https://github.com/webifycms/ext-user
- **WebifyCMS organization:** https://github.com/webifycms
- **Documentation:** https://webifycms.com/docs
- **Contributing:** [https://webifycms.com/contributing](/contributing)

Stay tuned for more updates as the infrastructure takes shape, and thank you for following the WebifyCMS journey.
