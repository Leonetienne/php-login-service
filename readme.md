# Set up environment:
```bash
sudo apt-get update

# Install php
sudo apt-get install php7.4-cli

# Install docker
# If using Windows, update your WSL-2 kernel
https://docs.microsoft.com/de-de/windows/wsl/install-win10#step-4---download-the-linux-kernel-update-package


# Install brew
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Add /home/linuxbrew/.linuxbrew/bin to your $PATH in ~/.bashrc
# Add this to your ~/.bashrc
export PATH="/home/linuxbrew/.linuxbrew/bin:$PATH"

# Install ddev
brew tap drud/ddev
brew install ddev

# Install Docker for WSL 2.0 on your windows end

# Install dependencies via composer
ddev composer install

# Install cert
brew install mkcert nss
mkcert install

```

# Start project
```
# Launch project
ddev launch
```

# Important after first start
```
# Set up database
Call 'setup database' in postman
```

# Debug rest api
Use [Postman](https://www.postman.com/), and open `php-login-service-postman-docs.json`.
