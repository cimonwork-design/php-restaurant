import urllib.request
import re

url = 'https://raw.githubusercontent.com/donnemartin/system-design-primer/master/README.md'
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
content = urllib.request.urlopen(req).read().decode('utf-8')

# find lines with images and get the nearest heading before it
lines = content.split('\n')
current_heading = "Start"
for i, line in enumerate(lines):
    if line.startswith('#'):
        current_heading = line.strip()
    m = re.search(r'images/(\w+\.(?:png|jpg))', line)
    if m:
        img_name = m.group(1)
        print(f"{img_name} => {current_heading}")
