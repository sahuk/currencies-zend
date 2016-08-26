import requests
import datetime
import json
import sqlite3
import logging
import time


BASE_URL = "https://openexchangerates.org/api/"
CURRENCY_URL = BASE_URL + "currencies.json"


conn = sqlite3.connect('currencies.sqlite3')
c = conn.cursor()
s = requests.Session()
try:
    r = s.get(CURRENCY_URL)
    j = r.json()
    for currency, name in j.iteritems():
        c.execute(
            "select rowid from currency_codes where currency=?",
            (currency, )
        )
        if c.fetchall():
            continue
        c.execute(
            """insert into
                currency_codes (
                    currency,
                    name
                )
            values (?, ?);
            """,
            (currency, name))
finally:
    conn.commit()
    conn.close()
