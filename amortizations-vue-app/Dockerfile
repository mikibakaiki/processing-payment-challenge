FROM node:16

# Set the working directory
WORKDIR /amortizations-vue-app

COPY ./amortizations-vue-app/package*.json ./

RUN npm install

# Copy the rest of the application
COPY ./amortizations-vue-app ./

# The port that Vite uses
EXPOSE 3000

# The command to run the app
CMD ["npm", "run", "dev"]