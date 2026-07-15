# pfSense AmneziaWG Package

Full integration of [amneziawg-go](https://github.com/amnezia-vpn/amneziawg-go) into pfSense 2.8.1+ with a native web GUI.

## Features

- Tunnel management via pfSense web interface
- All AmneziaWG obfuscation parameters: `Jc`, `Jmin`, `Jmax`, `S1`, `S2`, `H1`–`H4`
- Automatic generation of WireGuard configs from XML configuration
- Service control through rc.d script
- Cross‑compiled `.pkg` for **amd64** and **arm64** via GitHub Actions

## Building

Push this repository to GitHub, and the Actions workflow will automatically:

1. Clone `amneziawg-go` source
2. Cross‑compile the binary for FreeBSD
3. Create a `.pkg` file
4. Upload it as an artifact

To build locally (on FreeBSD or cross‑compile):
```bash
git clone https://github.com/amnezia-vpn/amneziawg-go
cd amneziawg-go
GOOS=freebsd GOARCH=amd64 go build -o amneziawg-go ./cmd/amneziawg-go
cp amneziawg-go /usr/local/bin/
# then assemble pfsense-files into staging and run pkg create