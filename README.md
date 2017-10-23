# Belga
Just another php http banner grab using *fsockopen* calls

## Requirements
- php >= 5.6 (7 Recommended) with threadsafe
- php-pthreads

## Usage
```
$ php belga.php
php belga.php -r ip-range -p port(s) [,-n needle] [,-t thread] [,-o output] [, --verbose]

                -r           IP RANGE (192.168.0.1:192.168.0.255)
                -p           PORTS (80 OR 80,8080,...)
                -n           NEEDLE "Tomcat", "apache", "http", etc...
                -t           THREADS (Default: 1)
                -o           OUTPUT (Default: output.txt)
                --verbose    VERBOSE MODE (Default: false)

                by @proclnas - v1.0.0
```

## Example usage
```
$ php belga.php -r 192.168.0.0:192.168.0.255 -p 80,8080 -t 25 -n "microsoft"
[+] 192.168.0.121:80
[+] Needle "microsoft" found in 192.168.0.121:80
[+] 192.168.0.122:80
[+] 192.168.0.123:80
[+] Needle "microsoft" found in 192.168.0.123:80
```

## Todo
- Create example folders
- Add cidr notation

## Contribute
```
Feel free to fork and send a pull request or open an issue
```

### License
```
The MIT License (MIT)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
