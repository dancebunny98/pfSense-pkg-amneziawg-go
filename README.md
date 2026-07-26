# pfSense-pkg-AmneziaWG

A dedicated **VPN → AmneziaWG** web interface for pfSense 2.8.1 (FreeBSD 15),
independent of the standard WireGuard package. It supports obfuscation
(Jc/Jmin/Jmax/S1/S2/H1-H4/I1-I5) and one-step import of existing `.conf` files,
and operates using either the kernel driver (`amnezia-kmod`) or userspace mode
(`amneziawg-go`) with automatic fallback.

> Tested on pfSense CE 2.8.1-RELEASE. The web interface is a derivative work
> based on `pfSense-pkg-WireGuard` (Apache License 2.0), renamed and
> adapted for AmneziaWG. ---
<img width="951" height="300" alt="image" src="https://github.com/user-attachments/assets/3f79a41d-4560-42ae-83ad-ed65951b143c" />
<img width="1223" height="1032" alt="image" src="https://github.com/user-attachments/assets/0ed8e1bc-62b7-4f71-8803-0f4f416fc438" />
<img width="952" height="1229" alt="image" src="https://github.com/user-attachments/assets/617e19ea-65bc-4e54-8bda-0e5696153603" />
<img width="950" height="294" alt="image" src="https://github.com/user-attachments/assets/e9e33f62-ebbd-4c7a-8842-208abc91ec9a" />
<img width="963" height="996" alt="image" src="https://github.com/user-attachments/assets/239011db-153e-4026-91b3-dca7b799767f" />
<img width="938" height="637" alt="image" src="https://github.com/user-attachments/assets/ddf536ef-4dd0-48bd-94a2-1dbb6de65fbf" />
<img width="940" height="470" alt="image" src="https://github.com/user-attachments/assets/ef4e954e-e172-42ac-869f-328d700f449b" />



## 1. Installing amneziawg-go / amnezia-tools packages

If you already have the pre-built `.pkg` files (attached to the GitHub release) —
simply install them:

```sh
ssh root@<PFSENSE_IP>
bash
cd /tmp
fetch <link to amneziawg-go-*.pkg from the release>
fetch <link to amnezia-tools-*.pkg from the release>
fetch <link to amnezia-kmod-*.pkg from the release>

pkg add amneziawg-go-*.pkg amnezia-tools-*.pkg
pkg add amnezia-kmod-*.pkg   # if downloaded

# if pfSense complains about an ABI mismatch (this is normal due to the custom FreeBSD snapshot):
pkg add -f amneziawg-go-*.pkg amnezia-tools-*.pkg amnezia-kmod-*.pkg

# prevent accidental pkg upgrade
pkg lock amneziawg-go amnezia-tools amnezia-kmod

awg --version
amneziawg-go --version
```

## 2. Installing the web interface

```sh
cd /tmp
fetch <link to pfSense-pkg-AmneziaWG.zip from the release>
mkdir -p /root/pfSense-pkg-AmneziaWG
cd /root/pfSense-pkg-AmneziaWG
tar -xf /tmp/pfSense-pkg-AmneziaWG.zip 2>/dev/null || unzip -o /tmp/pfSense-pkg-AmneziaWG.zip -d .

bash
sh install.sh
```

The `install.sh` script automatically:
- checks that `amneziawg-go`/`amnezia-tools` are installed (warns if not);
- copies all package files to the appropriate system paths; - runs `php -l` on each file and **stops** if a syntax error is found (ensuring the system isn't left in a partially updated state);
- creates `/usr/local/etc/amneziawg/` for the generated config files;
- calls the standard `awg_install()` function—registering the earlyshellcmd, interface group, Unbound ACL, `php_awg`, and `/usr/local/etc/rc.d/amneziawgd`;
- restarts the web configurator.

Afterward, open the GUI (press `Ctrl+F5`) and check **VPN → AmneziaWG** and **Status → AmneziaWG**.

### Next steps

- **VPN → AmneziaWG → Tunnels → Add Tunnel** — create a tunnel manually, or
- **VPN → AmneziaWG → Tunnels → Import Configuration** — paste a complete `.conf` file.

---

## 3. Removal

```sh
bash
cd /root/pfSense-pkg-AmneziaWG
sh uninstall.sh
```

By default, it **preserves** your tunnels/peers in `config.xml` (respecting the
"Keep Configuration" option on the Settings page) — so a later reinstallation
will automatically restore them. To completely wipe the configuration:

```sh
sh uninstall.sh --purge-config
```

`uninstall.sh` stops the service, shuts down tunnels, removes the interface group/
Unbound ACL/earlyshellcmd, deletes all package files, and restarts the
web configurator. It does not touch the FreeBSD packages (`amneziawg-go`/
`amnezia-tools`/`amnezia-kmod`) — those are separate:

```sh
pkg unlock amneziawg-go amnezia-tools amnezia-kmod
pkg delete amneziawg-go amnezia-tools amnezia-kmod
```

---

## 4. Features

- **VPN → AmneziaWG → Tunnels** — create/edit tunnels with obfuscation fields
(Jc, Jmin, Jmax, S1, S2, H1-H4, I1-I5) in a dedicated form section.
- **Import Configuration** — paste a full `.conf` file ([Interface] + one
or more [Peer] sections) — the tunnel and peers are parsed and saved
automatically. Validation is the same as for the standard form: nothing is
saved if an error occurs.
- **VPN → AmneziaWG → Peers** — manage peers separately.
- **VPN → AmneziaWG → Settings** — general package settings. - **Status → AmneziaWG** — tunnel status, handshakes, traffic, and table;
package versions (`amnezia-tools`, `amneziawg-go`, `amnezia-kmod`).
- Service start/stop icon next to the page title (similar to the
native WireGuard package) — implemented via the standard pfSense `$service_hook` mechanism.
- Automatic fallback to userspace (`amneziawg-go`) if the `amn` kernel driver
is unavailable — exactly as `awg-quick` does manually.
- Completely independent of the native WireGuard package: separate XML path in
`config.xml` (`installedpackages/amneziawg`), separate configuration directory
(`/usr/local/etc/amneziawg/`), separate service (`amneziawgd`), and separate
interface group.

---
## 5. Building packages from ports (if pre-built .pkg files are unavailable)

You need a separate, clean VM running **FreeBSD 15.0-RELEASE** (amd64)—ports cannot be built directly on pfSense itself.

```sh
pkg install git go123
echo 'USE_PACKAGE_DEPENDS_ONLY=yes' >> /etc/make.conf   # otherwise, it will pull dependencies from source (taking hours)
pkg update

mkdir -p /tmp/ports
cd /tmp/ports
fetch https://github.com/freebsd/freebsd-ports/archive/refs/heads/main.tar.gz -o ports.tar.gz
tar -xzf ports.tar.gz --strip-components=1
```

Before building, check the version against upstream (the port might lag months behind actual Amnezia releases):

```sh
grep -E 'GH_TAGNAME|DISTVERSION' /tmp/ports/net/amnezia-tools/Makefile
```
Compare with https://github.com/amnezia-vpn/amneziawg-tools/tags — if upstream has moved ahead, update `DISTVERSION=` in the Makefile to the current tag and run:
```sh
cd /tmp/ports/net/amnezia-tools && make makesum
```

Build:
```sh
cd /tmp/ports/net/amnezia-tools && make package
cd /tmp/ports/net/amneziawg-go && make package
cd /tmp/ports/net/amnezia-kmod && make package
```

The resulting `.pkg` files are located in the `work/pkg/` directory of each port; transfer them to pfSense via `scp` and install them as previously described.

---
## 6. Known issues and troubleshooting

### The `amn` kernel driver may conflict with `if_wg`

If you need both native WireGuard (`if_wg.ko`, which is always loaded on pfSense) and AmneziaWG simultaneously, `amnezia-kmod` might fail to load (resulting in an "already loaded or in kernel" error—a conflict regarding the clone driver slot, not the file itself). In this case, the package **automatically** switches to the `amneziawg-go` userspace daemon in the background; this is a fully functional setup, though slightly slower than the kernel driver. Both packages (native WireGuard and AmneziaWG) continue to operate side-by-side without issues.

### Both packages use the same `if_prefix` (`tun_wg`)

This behavior is inherited from the original WireGuard package. Each package tracks available interface numbers only within its own section of `config.xml`. While direct collisions have not been observed, it is advisable to verify the status when actively using both packages simultaneously:
```sh
ifconfig -a | grep tun_wg
```

### Slow save/delete operations

Saving or deleting a tunnel or peer triggers a synchronous DNS resolution for the endpoints of all active peers (a behavior inherited from the original WireGuard package architecture). If an endpoint is slow to resolve, the entire operation is delayed. In this release, every internal package command is wrapped in a `timeout 10` command; consequently, no operation can hang `php-fpm` for more than 10 seconds, even if the DNS server is completely unresponsive.

### Version synchronization with upstream

FreeBSD ports sometimes lag behind Amnezia's GitHub releases by several months. Before building, check `DISTVERSION` against
https://github.com/amnezia-vpn/amneziawg-tools/tags and
https://github.com/amnezia-vpn/amneziawg-go/tags.

---

## License / Code Origin

The web interface is a derivative work of `pfSense-pkg-WireGuard`
(Copyright (c) 2015-2025 Rubicon Communications, LLC (Netgate);
Copyright (c) 2021 R. Christian McDonald; Copyright (c) 2020 Ascrod),
licensed under the Apache License 2.0. Original copyright headers have been
preserved in all files.
