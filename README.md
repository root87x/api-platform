## System required
- php 8.2
- Api platform 3.1
- Mysql
- roadrunner 2.11.3
- symfony 6.2
- doctrine 2.13.2
- Varnish

## Configuration
```
###> Environment ###
cp example.env .env
###< Environment ###

###> Roadrunner ###
.rr.yaml
###< ###
```

### Migrations
- migrations/

### Build and UP project
- docker-compose up -d --build

### web interface
- 0.0.0.0:8081