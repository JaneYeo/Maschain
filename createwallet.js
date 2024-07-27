async function fetchDatabase() {
    try {
        const response = await fetch('Hackathon/database.json');
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return await response.json();
    } catch (error) {
        console.error('Error reading JSON file:', error);
    }
}

async function fetchUserdb() {
    try {
        const response = await fetch('Hackathon/users.json');
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return await response.json();
    } catch (error) {
        console.error('Error reading JSON file:', error);
    }
}

async function createUserWallet(user, data) {
    const https = require('https');

    const url = 'https://service-testnet.maschain.com/api/wallet/create-user';
    const clientId = '99f55f11db1771b614b8650d9069efceda923e04472114d4ba63d7ba7996307c';
    const clientSecret = 'sk_ebf43e9b8dca38c04b566f808f1492bcb4ff59fcaf3a36251890ed844293cc23';

    const payload = {
        name: user.name,
        email: user.email,
        ic: data.ic,
        phone: data.phonenumber
    };

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'client_id': clientId,
                'client_secret': clientSecret,
                'content-type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();
        console.log(result);
    } catch (error) {
        console.error('Error:', error);
    }
}

async function main() {
    const data = await fetchDatabase();
    const users = await fetchUserdb();

    if (data && users) {
        for (let user of users) {
            if (data.race === "Malay" && data.category === "B40" && data.alive === true) {
                await createUserWallet(user, data);
            }
        }
    }
}

main();
