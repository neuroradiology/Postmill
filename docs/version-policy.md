# Version number policy

Postmill follows the [Semantic Versioning 2.0.0](https://semver.org/) ('SemVer')
convention of assigning version numbers to each tagged release. In short, each
release is given a version number in the `MAJOR.MINOR.PATCH` format (e.g.
`4.2.1`), and these numbers increase depending on whether a release is 'major',
'minor', or a 'patch' (a bug fix). The SemVer spec describes these as follows:

> * MAJOR version when you make incompatible API changes,
> * MINOR version when you add functionality in a backwards-compatible manner,
>   and
> * PATCH version when you make backwards-compatible bug fixes.

The SemVer spec is reasonably clear, so it won't be repeated here, but we still
have to decide on what is 'backwards-compatible' and what an 'API' is for
Postmill's purposes. Postmill is an application interacted with by end-users, it
is not a library or framework that other programs build upon. We are not writing
code for consumption by programs, we are writing code for the consumption *by
humans*.

Thus, we define a backwards-compatible change as something that's merely
considered an evolution of the software. A backwards-*incompatible* change, by
contrast, is then significant alteration or removal of the software's core
functionality to the point where the two versions must be considered separate
pieces of software. A good rule of thumb is: if upgrading incurs significant
loss of functionality, it is a major breaking change.
