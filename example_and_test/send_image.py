from PIL import Image, ImageDraw, ImageFont
import requests
import io
import time
import datetime

def send_image_with_text(url, token, text):
    """
    Sends an image with specified text to a URL via POST.

    Parameters:
    - url: The URL to send the image to.
    - token: The token to include in the POST data.
    - text: The text to embed in the image.
    """
    # Create a blank image
    image_size = (640, 640)  # 640x640 pixels
    image = Image.new('RGB', image_size, color=(255, 255, 255))  # White background

    # Draw the text on the image
    draw = ImageDraw.Draw(image)
    try:
        # Use a default font
        font = ImageFont.truetype("arial.ttf", 40)
    except IOError:
        # Use default PIL font if no truetype font is available
        font = ImageFont.load_default()

    # Calculate text position for centering
    text_bbox = draw.textbbox((0, 0), text, font=font)  # Get bounding box
    text_width, text_height = text_bbox[2] - text_bbox[0], text_bbox[3] - text_bbox[1]
    text_position = ((image_size[0] - text_width) // 2, (image_size[1] - text_height) // 2)

    # Draw the text
    draw.text(text_position, text, fill=(0, 0, 0), font=font)  # Black text

    # Save the image to a BytesIO buffer
    buffer = io.BytesIO()
    image.save(buffer, format="PNG")
    buffer.seek(0)

    # Send the image via POST
    response = requests.post(
        url,
        files={'image': ('image.png', buffer, 'image/png')},
        data={'farm_token': token}
    )

    # Check response
    if response.status_code == 200:
        print(f"Successfully sent image with text '{text}' to {url}.")
    else:
        print(f"Failed to send image with text '{text}' to {url}. HTTP Status: {response.status_code}")
    print(response.text)

send_image_with_text('http://localhost/real/upload_img.php', 'testtoken',  datetime.datetime.now().strftime("%H:%M:%S %p"))

#LOOP
while True:
    time.sleep(5)
    send_image_with_text('http://localhost/real/upload_img.php', 'testtoken',  datetime.datetime.now().strftime("%H:%M:%S %p"))
