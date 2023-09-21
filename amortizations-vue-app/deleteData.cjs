const admin = require('firebase-admin');
const fs = require('fs');

// Initialize Firebase
const serviceAccount = require('./gop-challenge-firebase');

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount)
});

const db = admin.firestore();

async function deleteAllDocumentsInCollection() {
    const amortizationsCollection = db.collection("amortizations");
    const querySnapshot = await amortizationsCollection.get();

    // Iterate over each document and delete it
    for (const docSnapshot of querySnapshot.docs) {
        await amortizationsCollection.doc(docSnapshot.id).delete();
    }

    console.log('All documents deleted from amortizations collection.');
}

deleteAllDocumentsInCollection().catch(console.error);
