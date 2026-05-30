import json
import os

# Define the paths to your JSON files
files = [
    "scraper/agriculture/data_dump.json",
    "scraper/education/data_dump.json"
]

total = 0
for file_path in files:
    if os.path.exists(file_path):
        with open(file_path, "r", encoding="utf-8") as f:
            data = json.load(f)
            count = len(data.get("data", {}).get("hits", {}).get("items", []))
            print(f"{file_path}: {count} items")
            total += count

print(f"---")
print(f"Expected total in Database: {total}")