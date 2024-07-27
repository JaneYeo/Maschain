const checkTokenBalance = async () => {
    const apiUrl = 'https://service-testnet.maschain.com/api/token/balance';
    
    const key = "1a716398e73c7e2055cdab5aee60fdf86eee3f2a99c8141952eb7c8274b6f241";
    const secret = "sk_7d02e3f5281a2aed13f005fa6c37b3c5ff3bff86be179c500f8e9e1dddebc43b";

    const headers = {
      'client_id': key,
      'client_secret': secret,
      'content-type': 'application/json'
    };

    const body = {
      wallet_address: "0x3faa355e2B7E2107D0a396b1d71876962c3aBD22",
      contract_address: "0xa7e30c1c27BB46932Fc1466FF472e134d689B4D6"
    };
  
    try {
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(body)
    });
  
   
    const data = await response.json();
    console.log('Token balance:', data);
    return data;
  } catch (error) {
    console.error('Error checking token balance:', error);
  }
};

// Call the function
checkTokenBalance();