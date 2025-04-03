# OnBoarding Jorge 
TourCMS back office onboarding project.

> [!WARNING]
> This project is under development.

## Features

- **Login autentication panel**
- **Dashboard with the channel operators**
- **Info of the tours availables in the differents channels**


## Requirements

- Deploy local environment on **Docker**.
- **Redis 5.0.13** or later.


## Deploy

### Clone the repository

Clone this repository into your local machine.

```bash
$ git clone https://github.com/palisis-juanra/onboardingJorge
```

### Deploy the local develoment environment

Make sure that you have the TourCMS local environment deployed, so **you need to read the deployment documentation**.


### Make virtual hosting for the project

Once you have the local development enviroment properly setted, you have to modified the apache files to create a virtual host for our project.
**Assuming that you're in the tourcms-docker directory**, we have to navigate to the apache files and create a configuration with the name of the project.

```bash
$ cd apache/sites
$ touch onboardingJorge.conf
```

Open this file with your editor of preference and add the following configuration:

```bash
<VirtualHost *:80>
    ServerName www.onboarding.local
    ServerAlias onboarding.local
    DocumentRoot /var/www/html/onboarding

    <Directory "/var/www/html/onboarding">
        AllowOverride All
        Options -Indexes +FollowSymLinks
    </Directory>
    RewriteEngine On
    RewriteCond %{HTTP:X-Forwarded-Proto} =http
    RewriteRule .* https://%{HTTP:Host}%{REQUEST_URI} [L,R=permanent]
</VirtualHost>
```


### Modify docker-compose.yml

We will need to add a line into docker compose file, so in **the apache service** we have to add the following line in the volumes directive.

```yml
volumes:
    - ./onboarding:/var/www/html/onboarding
```

### Modify /etc/hosts

Since we don´t have any DNS server that resolve the server url for us, we need to set the hosts file to www.onboarding.local point to our localhost IP address.

```bash
127.0.0.1   wwww.onboarding.local onboarding.local
```

### Build the images

Next step is build the docker images to run the project, so we need to run the following command:

```bash
$ docker compose up --build -d
```

Access to the project on your browser: **http://www.onboarding.local**

> [!NOTE]
> *Once we have the images built, we don't need to run the before command in the future, It will be fine running **docker compose up -d***