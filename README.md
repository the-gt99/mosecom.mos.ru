# AirService

Сервис по расписанию обходит внешние источники и собтрает данные анализа возуха

## Установка
1. ```composer install```

1. ```cp .env.example .env```

1. ```php artisan key:generate```

1. Настройка .env

1. ```php artisan migrate```

## API

HOST_NAME = ```https://air.ykdev.ru``` 

```<HOST_NAME>/api/current?lat=<lat>&lon=<lon>```

Где ```<lat>``` - Широта,  ```<lon>``` - Долгота

Response:
```
[
  {
    "type": "mosecom",
    "name": "МосЭкоМониторинг",
    "stations": [
      {
        "id": 1,
        "name": "Наименование станции",
        "address": "Адрес станции",
        "lat": 55.75322,
        "lon": 37.622513,
        "indications": [
          {
            "id": 1,
            "name": "Метан",
            "particle_name": "CH4",
            "proportion": 0,
            "unit": 0,
            "measurement_at": 1620365674
          }
        ]
      }
    ]
  }
]
```
