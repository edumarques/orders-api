<div align="center">

# Orders API
Simple API that manages placement of orders. It also supports issuing and handling vouchers.

</div>

## Installation

The project can be installed and run using *Docker* and *docker-compose*.
To install *Docker*, visit its [official page](https://docs.docker.com/engine/install/).
And to install *docker-compose*, follow [these steps](https://docs.docker.com/compose/install/).
To help manage the project and execute commands in an easier way,
there is a set of [Make](https://www.gnu.org/software/make/) targets already configured.

Set up and run the project just by running the following command:

```sh
make start
```

## Useful tips

### List of available commands

```text
 👷 Makefile               
help                           Outputs this help screen
 🐳 Docker                 
setup                          Sets up dependencies for environment
build                          Builds container(s)
up                             Start container(s)
up-d                           Start container(s) in detached mode (no logs)
start                          Set up, build and start the containers
stop                           Stop container(s)
down                           Stop and remove container(s)
logs                           Show logs
logs-f                         Show live logs
ps                             Show containers' statuses
sh                             Connect to a container via SH
bash                           Connect to a container via BASH
php-sh                         Connect to the PHP FPM container via SH
php-bash                       Connect to the PHP FPM container via BASH
 ✅ Code Quality            
phpcs                          Run PHP Code Sniffer
phpcs-fix                      Run PHP Code Sniffer (fix)
phpstan                        Run PHPStan
lint                           Run PHP Code Sniffer and PHPStan
test                           Run tests, pass the parameter "args=" to run the command with arguments or options
test-cov                       Run tests and generate coverage report
 🧙 Composer               
composer                       Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
 🎶 Symfony                
sf                             List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
cc                             Clear the cache
```
