## General info
Use the existing codebase any way you like, but do try to complete the following assignments.
Commit your solution and send it the same way you got it. We will use it as a kick-off point for our interview and 
discuss architecture decisions, code performance and solution approaches.

It would be appreciated in the real world, but you can skip writing documentation. 
On the other hand, tests are very much appreciated, and it would be hard to persuade us that the code works otherwise.

### Running project
This project is a simplified app, that runs in CLI mode. 

If you are running PHP 8.1 and _composer_ locally, you can do the `composer install` from the `app` folder and then do 
`vendor/bin/phpunit` for running tests and `bin/app parse` for running the parser command.

If you prefer running things in Docker, we have provided a `docker-compose.yml` file, as well as `Makefile` for shorthand methods.

## Assignments

### One test is failing
Run the test suite and fix the problem

### Implement factory for Flights
Given the following JSON structure, can you implement a factory for `App\Domain\Flight` objects?

```json
{
  "registration": "OO-AAC",
  "from": "STN",
  "to": "BER",
  "scheduled_start": "2021-12-17T10:00:00+00:00",
  "scheduled_end": "2021-12-17T11:30:00+00:00",
  "actual_start": "2021-12-17T10:12:00+00:00",
  "actual_end": "2021-12-17T11:33:00+00:00"
}
```

### Analyze dataset
Given the `var/input.jsonl` dataset, can you write the code that does analysis and gives us the following answers:
1. Which were the three longest flights, by their actual duration?
2. Which Airline missed most of their scheduled landings? (for the sake of example, we'll use > 5 minutes between `scheduled_end` and `actual_end` as threshold for marking landing as missed. The example in the JSON structure above should be marked as on time, if the `actual_end` was `2023-12-17T11:36:00+00:00` then we should mark it as missed)
3. Which destination (value of the airport code in field `to`) had most overnight stays? (for the sake of example, we'll assume that landing before midnight and taking of after midnight counts as overnight stay).

You can run the analyzer via `App\Infrastructure\ParseCommand`.

### Bonus points

You can write the answers for bonus points here, in the README.md file.
1. Can you name all the countries where Airplanes from our example are registered?
2. If you had to spell your country code over the radio, how would you do it?

