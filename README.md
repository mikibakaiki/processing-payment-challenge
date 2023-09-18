<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laravel Payment Processing

This project implements a payment system that processes amortizations and payments, ensuring that payments are correctly handled.

## Assumptions

-   You have Docker and Docker-compose installed on your system. [Check Laravel's instructions for each OS](https://laravel.com/docs/10.x#laravel-and-docker)

-   The project wallet can never be negative
-   The date that will be checked to see if an Amortization is overdue is being calculated at runtime, and will be the current date and time of the execution.

## Database Relations

-   Each Amortization is associated with one Project.
-   Each Amortization can have multiple Payments associated with it.
-   Each Payment is associated with one Amortization.
-   Each Payment is associated with one Profile.
-   Each Payment is associated with one Promoter.
-   Each Profile can have multiple Payments associated with it.
-   Each Project can have multiple Amortizations associated with it.
-   Each Project is associated with one Promoter.
-   Each Promoter can have multiple Projects associated with it.
-   Each Promoter can have multiple Payments associated with it.

```mermaid
classDiagram
    Project "1" --> "*" Amortization : has
    Project "1" --> "1" Promoter : belongsTo
    Promoter "1" --> "*" Project : has
    Promoter "1" --> "*" Payment : has
    Amortization "1" --> "*" Payment : has
    Payment --> Amortization : belongsTo
    Payment --> Promoter : belongsTo
    Payment --> Profile : belongsTo

    class Project {
        +id: int
        +name: string
        +wallet_balance: double
    }

    class Promoter {
        +id: int
        +name: string
        +email: string
    }

    class Amortization {
        +id: int
        +schedule_date: datetime
        +state: string
        +amount: double
    }

    class Payment {
        +id: int
        +amount: double
        +state: string
    }

    class Profile {
        +id: int
        +name: string
        +email: string
    }
```

## Setup

This project is built with [Sail](https://laravel.com/docs/10.x/sail), a light-weight command-line interface for interacting with Laravel's default Docker development environment.

## Useful commands

`alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'`

`sail artisan migrate`

`sail up -d`

The migrate:fresh command will drop all tables from the database and then execute the migrate command:

`sail artisan migrate:fresh`

`sail artisan migrate:fresh --seed`

To run the queue on the database:

add this to the `.env` file `QUEUE_CONNECTION=database`
`sail artisan queue:table`

`sail artisan migrate`

`sail artisan queue:work redis`

`./vendor/bin/sail composer require predis/predis` - to install predis

`sail down`

`sysctl vm.overcommit_memory=1`

`sail artisan queue:work redis --max-jobs=100`

## Performance

**Note:** In this code, we are not using chunk. If you have a very large number of amortizations to process, and you want to use chunk to reduce the memory usage, you can modify the code to create a batch of jobs for each chunk and dispatch them separately. However, this will create multiple batches, and you will need to handle the completion and failure of each batch separately.

## Inspiration

[Table Pagination](https://tailwindui.com/components/application-ui/navigation/pagination)
