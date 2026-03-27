<style>
        /* 1. ALAPBEÁLLÍTÁSOK */
        body { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('img/blog_back.jpg') no-repeat center center fixed; 
            background-size: cover;
        }

        body.blog-page .page-content { 
            padding: 40px; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body.blog-page .content-section { 
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
            padding-top: 20px !important; 
        }

        body.blog-page .section-heading { margin-bottom: 30px; }

        /* 2. CÍM DIZÁJN (Üzenőfal Diamond) */
        body.blog-page .section-heading h1 {
            font-size: clamp(34px, 4vw, 50px);
            font-weight: 900;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            position: relative;
            display: inline-block;
            padding: 12px 18px 12px 58px;
            border-radius: 16px;
            -webkit-text-stroke: 2px #ffffff;
            paint-order: stroke fill;
            background: linear-gradient(135deg, rgba(255,255,255,0.10), rgba(180,220,255,0.14) 35%, rgba(0,0,0,0.12)),
                        radial-gradient(120% 140% at 0% 0%, rgba(90,160,255,0.22) 0%, rgba(0,0,0,0) 60%);
            border: 1px solid rgba(120,190,255,0.55);
            box-shadow: 0 14px 30px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.15);
            text-shadow: 0 10px 22px rgba(0,0,0,0.65);
            font-family: "Trebuchet MS","Segoe UI",sans-serif;
        }

        body.blog-page .section-heading h1::before {
            content: "◆";
            position: absolute;
            left: 20px; top: 50%;
            transform: translateY(-50%);
            color: #7fd0ff;
            font-size: 28px;
        }

        body.blog-page .section-heading h1 em { color: #7fd0ff; font-style: normal; }

        body.blog-page .section-heading p {
            font-size: clamp(18px, 2.2vw, 24px);
            color: rgba(235,245,255,0.96) !important;
            text-shadow: 0 8px 18px rgba(0,0,0,0.65);
            margin-top: 16px;
        }

        /* 3. POSZTOK ÉS ÜZENETEK */
        body.blog-page .feed {
            flex: none !important;
            height: auto !important;
            overflow-y: visible !important;
            padding-bottom: 60px;
        }

        body.blog-page .post-card {
            padding: 14px 16px;
            margin-bottom: 14px;
            border-radius: 14px;
            width: 92%;
            max-width: 720px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.18);
            border: 1px solid rgba(0,0,0,0.15) !important;
        }

        body.blog-page .other-post { background: #e6e6e6 !important; color: #000 !important; margin-left: 0; }
        body.blog-page .my-post { background: #45489a !important; color: #fff !important; margin-left: auto; margin-right: 25px; }

        body.blog-page .post-text {
            font-size: calc(1.25em + 1.5px);
            line-height: 1.5;
        }

        /* 4. BEVITELI MEZŐ (COMPOSER) */
        body.blog-page .composer {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #45489a;
        }

        textarea.form-control {
            font-size: 18px !important;
            line-height: 1.5;
            padding: 12px;
            background: #fff !important;
            color: #000 !important;
            min-height: 100px;
        }

        textarea.form-control:focus {
            border: 3px solid #2d5a27;
            box-shadow: 0 0 10px rgba(45, 90, 39, 0.2);
        }

        /* 5. REKLÁMOK - A HIBA JAVÍTÁSA ITT VAN */
        body.blog-page .ads-container {
            width: 125px; height: 600px; position: fixed; right: 20px; top: 100px;
            background: rgba(0,0,0,0.5); border: 1px solid #45489a; z-index: 10;
        }

        body.blog-page .mobile-ad-bar {
            display: none; position: fixed; bottom: 0; left: 0; width: 100%;
            background: #45489a; color: white; z-index: 20005; height: 50px; overflow: hidden;
        }

        body.blog-page .mobile-ad-train { display: flex; align-items: center; height: 50px; animation: adRunSide 15s linear infinite; }
        @keyframes adRunSide { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        /* 6. LÁBJEGYZET (PILL) */
        body.blog-page .site-footer-fixed {
            position: static;
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        body.blog-page .site-footer-fixed__pill {
            background: #fff; color: #000; padding: 6px 20px;
            border: 2px solid #d4af37; border-radius: 25px;
            font-family: 'Georgia', serif; font-style: italic;
        }

        /* 7. MÉDIA LEKÉRDEZÉSEK (RESPONSIVE) */
        @media (max-width: 1001px) {
            body.blog-page .ads-container { display: none !important; }
            body.blog-page .mobile-ad-bar { display: block !important; } /* Itt kényszerítjük ki 1000px alatt */
            
            body.blog-page .page-content { 
                display: block !important;
                padding-top: 105px !important;
                padding-bottom: 160px !important; 
            }

            body.blog-page .site-footer-fixed {
                position: fixed;
                left: 50%; transform: translateX(-50%);
                bottom: 65px;
                z-index: 20000;
            }
        }

        @media (max-width: 767px) {
            body.blog-page .blog-mobile-nav {
                display: block !important;
                position: fixed !important;
                top: 0; left: 0; width: 100%; height: 80px;
                background: rgba(250,250,250,.95);
                z-index: 21000 !important;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }

            body.blog-page #blog-mobile-menu {
                display: none; position: fixed; top: 80px; left: 0; width: 100%;
                background: rgba(0,0,0,0.95); z-index: 20500;
            }
            
            body.blog-page #blog-mobile-menu a {
                display: block; padding: 12px 0; text-align: center; color: #fff !important; text-decoration: none;
            }

            body.blog-page .blog-mobile-nav__toggle .icon-bar {
                display: block; width: 22px; height: 2px; background-color: #3498db; margin: 4px 0;
            }
        }
    </style>