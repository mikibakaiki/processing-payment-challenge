FROM node:16

# Set the working directory
WORKDIR /vite-app

COPY ./package*.json .
RUN npm install && \
    ./node_modules/.bin/vite --version

# Copy the rest of the application
COPY . .

# The port that Vite uses
EXPOSE 3000

# The command to run the app
CMD ["npm", "run", "dev"]
