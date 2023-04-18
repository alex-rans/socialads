# Calculator of the Dark Sun
This mysterious calculator was forged in the depths of the Javascript Catacombs, infused with the dark energies of the Sunless
Loosely Typed Realms.

The Calculator of the Dark Sun is a powerful tool that allows its wielder to make precise calculations of the arcane
energies that flow through the world of tech. With its help, even the most complex tasks and abilities can be
perfectly timed and executed, giving its owner a fearsome advantage in planning.

But beware, for the Calculator of the Dark Sun is not without its risks. Its powers are not fully understood, and some
say that it can even bend the laws of reality if used improperly. Some even whisper that the calculator's true purpose
is to open a portal to the Abyss itself, allowing its wielder to commune with the dark forces that lurk there.

In the hands of a skilled manager, however, the Calculator of the Dark Sun can be a powerful ally, helping to turn
the tide of even the most difficult deadlines. Just be sure to use it wisely, lest you find yourself consumed by the
darkness it contains.

---

# calculator# Setup

Run ```lando start``` to get your project up and running.

If you apply changes to the .lando.yml file it is recommended to run ```lando rebuild```.

## Security certificates

If this is your first time running Lando, add security certificates for Lando projects with the following command(s).

### Mac

```
sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain ~/.lando/certs/lndo.site.pem
```

### Windows

Remember to replace `ME` with your username.

```
certutil -addstore -f "ROOT" C:\Users\ME\.lando\certs\lndo.site.pem
```

### Linux

```
sudo cp -r ~/.lando/certs/lndo.site.pem /usr/local/share/ca-certificates/lndo.site.pem
sudo cp -r ~/.lando/certs/lndo.site.crt /usr/local/share/ca-certificates/lndo.site.crt
sudo update-ca-certificates
```

For more information, visit [Docker on Confluence](https://confluence.hosted-tools.com/display/HRT/Docker).