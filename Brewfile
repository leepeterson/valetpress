tap "caskroom/versions"
tap "homebrew/core"
tap "homebrew/services"
tap "caskroom/cask"
cask_args appdir: "~/Applications"
brew "libidn"
brew "openssl"
brew "nss"
brew "mkcert"
brew "wget"
brew "curl", args: ["with-nghttp2","with-openssl"]
brew "git", args: ["with-openssl","with-curl"]
brew "hub"
brew "node"
brew "php", args: ["with-tidy-html5"], link: true, restart_service: :changed
brew "brew-php-switcher"
brew "composer"
brew "wp-cli"
brew "wp-cli-completion"
brew "imagemagick"
brew "dnsmasq", args: ["with-dnssec","with-libidn"], restart_service: true
brew "sqlite"
brew "mariadb", link: true, conflicts_with: ["mysql"], restart_service: :changed
tap "denji/nginx", pin: true
brew "nginx-full", args: ["with-http2"], link: true, conflicts_with: ["nginx"], restart_service: true
brew "jq"
brew "chrome-cli"
brew "imageoptim-cli"
brew "jpegoptim"
brew "ffmpeg"
tap "mas-cli/tap", pin: true
brew "mas"
cask "iterm2"
cask "sequel-pro"
cask "google-chrome-canary"
cask "batchmod"
cask "codekit"
cask "firefox-developer-edition"
