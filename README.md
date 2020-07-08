# Gmod-ControlServer [Bêta 1.0]

I created a tool not too long ago because I had no administration panel because my server was on LinuxGSM [VPS]. So I made Gmod-ControlServer which I still develop today.

## Installation

For installation you must have : 
✔️ - Website
✔️ - SteamAPI
✔️ - GmodServer (not on a private network)


You then have a config file to fill in your information!
You also need to configure your Steam API in steamauth

```php
// WEBSITE

    $steamid64 = "76561198336390464"; // ADMIN SteamID64
    $domain = "http://localhost"; // DOMAIN OF WEBSITE

// SERVER

    $ip = "127.0.0.1"; // SERVER IP
    $port = "27015"; // SERVER PORT
```

## Usage

Then you then have a login page

If you have put your SteamID64 in the configuration you will have access to a panel like that.  
![Image](https://i.imgur.com/HnFOk4o.png)

⚠️ : PLEASE NOTE the buttons with actions are not functional at the moment. [RCON CONCERNED]

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)
