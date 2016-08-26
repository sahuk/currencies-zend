create table if not exists currency_rates (
	currency char (3),
	rate float,
	day integer (7),
	timestamp integer (10),
	basecurrency char (3)
);

create table if not exists currency_codes (
	currency char (3),
	name text
);

create unique index if not exists currency_rates_currency_day_index
	on currency_rates (currency, day);

create index if not exists currency_rates_currency_index
	on currency_rates (currency);

create unique index if not exists currency_codes_currency_index
	on currency_codes (currency)
	where currency is not null;
