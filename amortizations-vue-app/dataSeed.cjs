const admin = require('firebase-admin');
const fs = require('fs');

// Initialize Firebase
const serviceAccount = require('./gop-challenge-firebase');

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount)
});

const db = admin.firestore();

// Read JSON from a file
const data = JSON.parse(fs.readFileSync('./amortSeed.json', 'utf8'));

const updatedData = data.map((item, index) => {
    return { id: index + 1, ...item };
  });

const seedFirestore = async () => {
    const batch = db.batch();
    const amortizationsCollection = db.collection('amortizations');

    updatedData.forEach(doc => {
        const docRef = amortizationsCollection.doc(); // This will auto-generate a new doc ID
        batch.set(docRef, doc);
    });

    // Commit the batch
    await batch.commit();
    console.log('Firestore seeded successfully.');
};

seedFirestore().catch(error => {
    console.error('Error seeding Firestore:', error);
});
