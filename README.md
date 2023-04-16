# Babysitter Kata
An application based on requirements found as part of the [babysitter kata](https://gist.github.com/jameskbride/5482722).

## Assumptions
- We're only given some small details about when the sitter may arrive. It appears possible that there could be
overlap in timing depending on arrival, bedtime and departure. We're going to assume that we should charge the
post-midnight rate after midnight _even if_ the bedtime is after midnight.
- Given that there is the potential for an 11-hour shift, we're going to assume that we have to account for
overtime pay and calculate that rate at time-and-a-half.

## Questions
- What is the preferred calculation of the time-and-a-half rate? Should we be using a blended rate or the rate 
for the currently active job?
