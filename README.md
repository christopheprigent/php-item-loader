# php-item-loader

technical test

## input

Scenario :

Nous devons récolter une grande quantité de données fournies par un tiers.

Compte tenu du volume, il est impossible de tout charger en mémoire en amont du traitement.

L'API tierce ayant un API rate, nous ne pouvons pas non plus faire un appel pour chaque item,

sans quoi nous serons bloqués très rapidement.

Pour chaque item reçu, nous effectuons un traitement. Celui-ci est suffisamment

long pour que ce soit rentable de paralléliser le travail.

Chaque item sera donc envoyé à un service distant pour être traité.

Une fois le résultat du traitement reçu, il devient disponible pour notre client (notre object).

Afin que l'objet soit simple d'utilisation, nous devons pouvoir itérer dessus avec un simple foreach.

Un exemple d'utilisation ci-après.

A vous d'implémenter une classe qui résous notre use case.

Vous expliquez vos hypothèses, choix et tradeoffs.

## my understanding

```monodraw                                                                                                                                                                               
                                                                                      ┌───────────┐
                                                                                      │  EXT API  │
                                                     ┌───────────────┐                └───────────┘
                                                     │               │                      ▲      
┌───────────────┐     ┌───────────┐                  │               │   resquest PAGE_SIZE │      
│               │     │           │    compute item  │   item-load   │         items        │      
│              ◀┼─────│  Threads  │◀─ ─ ─ ─ ─────────│               │──────────────────────┘      
│    compute    │     │ ┌─────────┴─┐       │        │               │                             
│   endpoint    │     └─┤           │                │               │                             
│              ◀┼───────│  Threads  │◀─ ─ ─ ┘        └───────────────┘                             
│               │       │           │                                                              
└───────────────┘       └───────────┘                                                              
```                                                                                                                                                                                                                                           

## some details

### PAGE_SIZE

 ratio between MEMORY_AVAILABLE and AVERAGE_OBJ_SIZE (empirical)
 
 we must maximize this value to avoid rate limited issue.
 
`define('PAGE_SIZE', intval(MEMORY_AVAILABLE/AVERAGE_OBJ_SIZE));`

### external API

we assume that external API provide basic pagination  :

```json
page {
   size
   totalElements
   totalPages
   number
 }
 ```

### threading

 we use parallel, https://www.php.net/manual/fr/book.parallel.php

### tradeoffs

  ~~using parallel on the same server who call the external API reduce the available memory~~

  to reduce the complexity,
  
  we should consider spliting the project in 2 parts :

  - retrieve and secure the external data
  - compute them

 but this will increase the global time to do the job

## output

```shell
$ php main.php
Current : item -> 0 | page -> 0
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 1 | page -> 1
I should be a large amount of computed data
Current : item -> 2 | page -> 1
I should be a large amount of computed data
Current : item -> 3 | page -> 1
I should be a large amount of computed data
Current : item -> 4 | page -> 1
I should be a large amount of computed data
Current : item -> 5 | page -> 1
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 6 | page -> 2
I should be a large amount of computed data
Current : item -> 7 | page -> 2
I should be a large amount of computed data
Current : item -> 8 | page -> 2
I should be a large amount of computed data
Current : item -> 9 | page -> 2
I should be a large amount of computed data
Current : item -> 10 | page -> 2
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 11 | page -> 3
I should be a large amount of computed data
Current : item -> 12 | page -> 3
I should be a large amount of computed data
Current : item -> 13 | page -> 3
I should be a large amount of computed data
Current : item -> 14 | page -> 3
I should be a large amount of computed data
Current : item -> 15 | page -> 3
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 16 | page -> 4
I should be a large amount of computed data
Current : item -> 17 | page -> 4
I should be a large amount of computed data
Current : item -> 18 | page -> 4
I should be a large amount of computed data
Current : item -> 19 | page -> 4
I should be a large amount of computed data
Current : item -> 20 | page -> 4
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 21 | page -> 5
I should be a large amount of computed data
Current : item -> 22 | page -> 5
I should be a large amount of computed data
Current : item -> 23 | page -> 5
I should be a large amount of computed data
Current : item -> 24 | page -> 5
I should be a large amount of computed data
Current : item -> 25 | page -> 5
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 26 | page -> 6
I should be a large amount of computed data
Current : item -> 27 | page -> 6
I should be a large amount of computed data
Current : item -> 28 | page -> 6
I should be a large amount of computed data
Current : item -> 29 | page -> 6
I should be a large amount of computed data
Current : item -> 30 | page -> 6
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 31 | page -> 7
I should be a large amount of computed data
Current : item -> 32 | page -> 7
I should be a large amount of computed data
Current : item -> 33 | page -> 7
I should be a large amount of computed data
Current : item -> 34 | page -> 7
I should be a large amount of computed data
Current : item -> 35 | page -> 7
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 36 | page -> 8
I should be a large amount of computed data
Current : item -> 37 | page -> 8
I should be a large amount of computed data
Current : item -> 38 | page -> 8
I should be a large amount of computed data
Current : item -> 39 | page -> 8
I should be a large amount of computed data
Current : item -> 40 | page -> 8
Doing stuff, calling external api
external api OK, compute datas in parallel
I should be a large amount of computed data
Current : item -> 41 | page -> 9
I should be a large amount of computed data
Current : item -> 42 | page -> 9
I should be a large amount of computed data
```
