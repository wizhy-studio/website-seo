const http = require('http');
const fs = require('fs');
const path = require('path');
const { spawn } = require('child_process');

const chromePath = fs.existsSync('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe')
  ? 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe'
  : 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';

function captureCDP(port, width, height, isMobile, outPath, scrollToY = 0) {
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

          if (scrollToY > 0) {
            ws.send(JSON.stringify({
              id: msgId++,
              method: 'Runtime.evaluate',
              params: { expression: `window.scrollTo(0, ${scrollToY});` }
            }));
          }

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

    // Get section offsets
    const offsets = await new Promise((resolve) => {
      http.get('http://localhost:9228/json', (res) => {
        let data = '';
        res.on('data', c => data += c);
        res.on('end', () => {
          const targets = JSON.parse(data);
          const page = targets.find(t => t.type === 'page');
          const ws = new WebSocket(page.webSocketDebuggerUrl);
          ws.onopen = () => {
            ws.send(JSON.stringify({
              id: 1,
              method: 'Runtime.evaluate',
              params: {
                expression: `JSON.stringify({
                  services: document.getElementById('services')?.offsetTop || 0,
                  testimonials: document.getElementById('testimonials')?.offsetTop || 0,
                  leadMagnet: document.getElementById('leadMagnetForm')?.closest('section')?.offsetTop || 0,
                  contact: document.getElementById('contact')?.offsetTop || 0,
                  footer: document.querySelector('.footer')?.offsetTop || 0
                })`,
                returnByValue: true
              }
            }));
          };
          ws.onmessage = (e) => {
            const resp = JSON.parse(e.data);
            if (resp.id === 1 && resp.result && resp.result.result) {
              const val = JSON.parse(resp.result.result.value);
              ws.close();
              resolve(val);
            }
          };
        });
      });
    });

    console.log('Section Offsets:', offsets);

    // 1. Desktop Services Screenshot
    await captureCDP(9228, 1440, 900, false, path.resolve(__dirname, 'screenshot_services.png'), (offsets.services || 800) - 80);

    // 2. Desktop Testimonials Screenshot
    await captureCDP(9228, 1440, 900, false, path.resolve(__dirname, 'screenshot_testimonials.png'), (offsets.testimonials || 2600) - 80);

    // 3. Desktop Lead Magnet Screenshot
    await captureCDP(9228, 1440, 900, false, path.resolve(__dirname, 'screenshot_leadmagnet.png'), (offsets.leadMagnet || 3600) - 80);

    // 4. Desktop Footer Screenshot
    await captureCDP(9228, 1440, 900, false, path.resolve(__dirname, 'screenshot_footer.png'), (offsets.footer || 4600) - 80);

    // 5. Mobile Screenshot
    await captureCDP(9228, 390, 844, true, path.resolve(__dirname, 'screenshot_mobile.png'), 0);

    // 6. Tablet Screenshot
    await captureCDP(9228, 768, 1024, true, path.resolve(__dirname, 'screenshot_tablet.png'), 0);

    child.kill();
    server.close();
    try { fs.rmSync(tempDir, { recursive: true, force: true }); } catch (e) {}
    console.log('✨ All CDPs captured!');
  });
}

runQA();
