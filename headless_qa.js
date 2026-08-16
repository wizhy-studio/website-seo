const http = require('http');
const fs = require('fs');
const path = require('path');
const { spawn } = require('child_process');

const chromePath = fs.existsSync('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe')
  ? 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe'
  : 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';

function captureCDP(port, width, height, isMobile, outPath) {
  return new Promise((resolve) => {
    http.get(`http://localhost:${port}/json`, (res) => {
      let data = '';
      res.on('data', c => data += c);
      res.on('end', () => {
        const targets = JSON.parse(data);
        const page = targets.find(t => t.type === 'page');
        if (!page) { resolve(false); return; }
        const ws = new WebSocket(page.webSocketDebuggerUrl);

        let msgId = 1;
        ws.onopen = () => {
          // Set device emulation
          ws.send(JSON.stringify({
            id: msgId++,
            method: 'Emulation.setDeviceMetricsOverride',
            params: {
              width: width,
              height: height,
              deviceScaleFactor: 2,
              mobile: isMobile
            }
          }));

          // Wait a moment then capture screenshot
          setTimeout(() => {
            ws.send(JSON.stringify({
              id: 999,
              method: 'Page.captureScreenshot',
              params: { format: 'png' }
            }));
          }, 800);
        };

        ws.onmessage = (event) => {
          const resp = JSON.parse(event.data);
          if (resp.id === 999 && resp.result && resp.result.data) {
            fs.writeFileSync(outPath, Buffer.from(resp.result.data, 'base64'));
            console.log(`✅ Saved ${path.basename(outPath)} (${fs.statSync(outPath).size} bytes)`);
            ws.close();
            resolve(true);
          }
        };

        ws.onerror = () => resolve(false);
      });
    });
  });
}

async function runQA() {
  const server = http.createServer((req, res) => {
    let filePath = path.join(__dirname, req.url === '/' ? 'index.html' : req.url);
    if (fs.existsSync(filePath)) {
      let ext = path.extname(filePath);
      let contentType = ext === '.css' ? 'text/css' : ext === '.js' ? 'application/javascript' : 'text/html';
      res.writeHead(200, { 'Content-Type': contentType });
      fs.createReadStream(filePath).pipe(res);
    } else {
      res.writeHead(404);
      res.end();
    }
  });

  server.listen(8099, async () => {
    const tempDir = path.join(require('os').tmpdir(), 'chrome_qa_cdp_' + Math.random().toString(36).substring(2));
    const child = spawn(chromePath, [
      '--headless=new',
      '--remote-debugging-port=9228',
      '--user-data-dir=' + tempDir,
      'http://localhost:8099/'
    ]);

    await new Promise(r => setTimeout(r, 1500));

    // 1. Desktop Screenshot
    await captureCDP(9228, 1440, 900, false, path.resolve(__dirname, 'screenshot_desktop.png'));

    // 2. Mobile Screenshot (iPhone 14 / modern smartphone: 390x844)
    await captureCDP(9228, 390, 844, true, path.resolve(__dirname, 'screenshot_mobile.png'));

    // 3. Tablet Screenshot (iPad / Tablet: 768x1024)
    await captureCDP(9228, 768, 1024, true, path.resolve(__dirname, 'screenshot_tablet.png'));

    child.kill();
    server.close();
    try { fs.rmSync(tempDir, { recursive: true, force: true }); } catch (e) {}
    console.log('✨ All CDPs captured!');
  });
}

runQA();
