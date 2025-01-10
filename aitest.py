import mysql.connector
import firebase_admin
from firebase_admin import credentials, messaging
import time
from google.auth import default

credentials, project_id = default()

# Initialize the Firebase Admin SDK
cred_obj = credentials.Certificate('seaweedfyp-firebase-adminsdk-ky1sy-0eaca81589.json')
default_app = firebase_admin.initialize_app(cred_obj)


host = "localhost"  # or the IP address of your MySQL server
user = "seaweedadmin"
password = "SeaweedFarmingIot2024"
database = "seaweed_farming"
try:
    # Establish a connection
    connection = mysql.connector.connect(
        host=host,
        user=user,
        password=password,
        database=database
    )

    if connection.is_connected():
        print("Connected to MySQL database")

except mysql.connector.Error as e:
    print(f"Error connecting to MySQL: {e}")

    
import vertexai
from vertexai.generative_models import GenerativeModel, Part

# TODO(developer): Update and un-comment below line
# PROJECT_ID = "your-project-id"
vertexai.init(project=PROJECT_ID, location="us-central1")

model = GenerativeModel("gemini-1.5-flash-002")

# Use Part.from_url to load an image from a URL
image_file = Part.from_url(
    "https://example.com/path-to-your-image.jpg", "image/jpeg"
)

# Query the model
response = model.generate_content([image_file, "what is this image?"])
print(response.text)

# Example response:
# That's a lovely overhead flatlay photograph of blueberry scones.
# The image features:
# * **Several blueberry scones:** These are the main focus,
# arranged on parchment paper with some blueberry juice stains.


