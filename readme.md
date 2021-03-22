
# Important notice!!
This software has been developed as a one-time educational project!  
This project has not been tested sufficiently for use in any large-scale project!  
This project is NOT planned to receive any future updates whatsoever.  
If you know what you are doing, feel free to use it for whatever you want or to modify it however you want.

# Set up environment:
```bash
sudo apt-get update

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


```
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
