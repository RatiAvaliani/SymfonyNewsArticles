class ajax {
    format = "application/ld+json";
    method;
    constructor(url, method= "POST", body=null) {
        this.method = method;
        let headers = {
            "Content-Type": this.format,
        };

        if (localStorage.getItem('token') !== null) {
            headers['Authorization'] = 'Bearer ' + localStorage.getItem('token');
        }

        let req = {
            method: this.method,
            headers: headers
        };

        if (body) req.body = JSON.stringify(body);

        this.request = new Request(url, req);
    }

    async receive (type = 'json') {
        try {
            let result = await fetch(this.request);
            if (type === 'json') {
                result = await result.json();
            }
            return result;
        } catch (error) {
            console.error("Error ->>", error);
        }
    }
}