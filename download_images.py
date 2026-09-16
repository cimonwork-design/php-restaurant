import os
import urllib.request
import ssl

ssl._create_default_https_context = ssl._create_unverified_context

os.makedirs("images_chuong3", exist_ok=True)

images = [
    {
        "name": "clean_architecture.jpg",
        "url": "https://blog.cleancoder.com/uncle-bob/images/2012-08-13-the-clean-architecture/CleanArchitecture.jpg"
    },
    {
        "name": "iso_25010_quality_model.png",
        "url": "https://iso25000.com/images/figures/en/iso25010_en.png"
    },
    {
        "name": "strangler_fig_pattern.png",
        "url": "https://martinfowler.com/bliki/images/strangler/strangler.png"
    },
    {
        "name": "clean_architecture_layers.png",
        "url": "https://raw.githubusercontent.com/jasontaylordev/CleanArchitecture/main/CleanArchitecture.png"
    },
    {
        "name": "microservices_pattern.png",
        "url": "https://microservices.io/i/microservices.png"
    },
    {
        "name": "event_driven_architecture.png",
        "url": "https://raw.githubusercontent.com/aws-samples/aws-serverless-airline-booking/develop/media/architecture.png"
    },
    {
        "name": "mvc_architecture_overview.png",
        "url": "https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/MVC-Process.svg/1000px-MVC-Process.svg.png"
    },
    {
        "name": "software_architecture_evolution.png",
        "url": "https://raw.githubusercontent.com/dotnet-architecture/eShopOnContainers/dev/img/eshop-architecture.png"
    }
]

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
}

for img in images:
    target_path = os.path.join("images_chuong3", img["name"])
    if os.path.exists(target_path) and os.path.getsize(target_path) > 1000:
        print(f"Already exists: {target_path} ({os.path.getsize(target_path)} bytes)")
        continue
    try:
        print(f"Downloading {img['name']} from {img['url']}...")
        req = urllib.request.Request(img["url"], headers=headers)
        with urllib.request.urlopen(req, timeout=15) as resp:
            data = resp.read()
            with open(target_path, "wb") as f:
                f.write(data)
        print(f"Success: {target_path} ({len(data)} bytes)")
    except Exception as e:
        print(f"Failed {img['name']}: {e}")

print("Download process completed.")
