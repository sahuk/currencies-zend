import requests
import datetime
import json
import sqlite3
import logging
import time
import config


APP_ID = config.APP_ID
BASE_URL = "https://openexchangerates.org/api/"
HISTORICAL_URL = BASE_URL + "historical/{}.json"
payload = {'app_id': APP_ID}


def days(timerange, step=datetime.timedelta(weeks=1)):
    "All the mondays between today and today-timerange"
    today = datetime.date.today()
    start = today - datetime.timedelta(days=today.weekday())
    day = start
    while day > today - timerange:
        yield day
        day = day - step


conn = sqlite3.connect('currencies.sqlite3')
c = conn.cursor()
s = requests.Session()
try:
    for day in days(datetime.timedelta(days=365*15)):
        url = HISTORICAL_URL.format(day.isoformat())
        c.execute(
            "select rowid from currency_rates where day=?",
            (day.toordinal(), )
        )
        if c.fetchall():
            continue
        print day
        r = s.get(url, params=payload)
        j = r.json()
        unixtime = int(time.time())
        for currency, rate in j['rates'].iteritems():
            c.execute(
                """insert into
                    currency_rates (
                        currency,
                        rate,
                        day,
                        timestamp,
                        basecurrency
                    )
                values (?, ?, ?, ?, ?);
                """,
                (currency, rate, day.toordinal(),
                 unixtime, j['base']))
        time.sleep(0.5)
finally:
    conn.commit()
    conn.close()
