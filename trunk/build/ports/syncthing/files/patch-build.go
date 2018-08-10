--- build.go.orig	2018-07-10 17:40:06.000000000 +0200
+++ build.go	2018-08-10 10:44:01.000000000 +0200
@@ -211,14 +211,14 @@
 		if err != nil {
 			log.Fatal(err)
 		}
+		os.Setenv("GOPATH", gopath)
+		log.Println("GOPATH is", gopath)
 		if !noBuildGopath {
-			lazyRebuildAssets()
 			if err := buildGOPATH(gopath); err != nil {
 				log.Fatal(err)
 			}
+			lazyRebuildAssets()
 		}
-		os.Setenv("GOPATH", gopath)
-		log.Println("GOPATH is", gopath)
 	} else {
 		inside := false
 		wd, _ := os.Getwd()
@@ -1260,7 +1260,7 @@
 
 func buildGOPATH(gopath string) error {
 	pkg := filepath.Join(gopath, "src/github.com/syncthing/syncthing")
-	dirs := []string{"cmd", "lib", "meta", "script", "test", "vendor"}
+	dirs := []string{"cmd", "gui", "lib", "meta", "script", "test", "vendor"}
 
 	if debug {
 		t0 := time.Now()
