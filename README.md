# Babysitter Kata
An application based on requirements found as part of the [babysitter kata](https://gist.github.com/jameskbride/5482722).

## Assumptions
- We're only given some small details about when the sitter may arrive. It appears possible that there could be
overlap in timing depending on arrival, bedtime and departure. We're going to assume that we should charge the
post-midnight rate after midnight _even if_ the bedtime is after midnight.
- Related to the previous item, we will assume that the babysitter is maximizing their earnings. Since we don't
have fractional hours, we will calculate times in a manner that result in the most time at the highest available
pay rate. For instance, if the arrival time is 8:49pm, we'd log an arrival of 8:00pm. If the departure time is
10:01pm, we'd log a departure time of 11:00pm.
- Given that there is the potential for an 11-hour shift, we're going to assume that we have to account for
overtime pay and calculate that rate at time-and-a-half.

## Questions
- What is the preferred calculation of the time-and-a-half rate? Should we be using a blended rate or the rate 
for the currently active job? Implemented [ADP's calculation of overtime for multiple pay rates](https://www.adp.com/resources/articles-and-insights/articles/h/how-to-calculate-overtime-pay.aspx).
