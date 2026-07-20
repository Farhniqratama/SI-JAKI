import sys
from PIL import Image

img_path = 'assets/images/logo-sijaki-sidebar.png'
out_path = 'assets/images/logo_square.png'

img = Image.open(img_path)
width, height = img.size

# Make a square background (white)
size = max(width, height)
# Add a little padding (15%)
size = int(size * 1.15)

# Create white square
square = Image.new('RGB', (size, size), (255, 255, 255))

# Paste image in center
offset = ((size - width) // 2, (size - height) // 2)

if img.mode in ('RGBA', 'LA') or (img.mode == 'P' and 'transparency' in img.info):
    square.paste(img, offset, img)
else:
    square.paste(img, offset)
    
square.save(out_path, format='PNG')
print(f"Created {out_path} with size {size}x{size}")
