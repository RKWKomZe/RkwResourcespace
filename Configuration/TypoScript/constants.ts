
plugin.tx_rkwresourcespace_import {
	view {
		# cat=plugin.tx_rkwresourcespace_import/file; type=string; label=Path to template root (FE)
		templateRootPath = EXT:rkw_resourcespace/Resources/Private/Templates/

		# cat=plugin.tx_rkwresourcespace_import/file; type=string; label=Path to template partials (FE)
		partialRootPath = EXT:rkw_resourcespace/Resources/Private/Partials/

		# cat=plugin.tx_rkwresourcespace_import/file; type=string; label=Path to template layouts (FE)
		layoutRootPath = EXT:rkw_resourcespace/Resources/Private/Layouts/
	}
	persistence {
		# cat=plugin.tx_rkwresourcespace_import//a; type=string; label=Default storage PID for logs
		storagePid =
	}
	settings {

		# cat=plugin.tx_rkwresourcespace_import//a; type=string; label=the upload destination inside fileadmin (e.g "/media/images/Medienpool/")
		uploadDestination =

		# cat=plugin.tx_rkwresourcespace_import//a; type=string; label=local system storage for buffering files (e.g. "/tmp/")
		# !! path always with closing slash !!
		localBufferDestination = /tmp/

		# cat=plugin.tx_rkwresourcespace_import//a; type=string; label=if set, only request from this IPs will granted (commaseparated list)
		ipRestriction =

		# cat=plugin.tx_rkwresourcespace_import//a; type=integer; label=Log activities in database
		logActivitiesInDb = 1

		# cat=plugin.tx_rkwresourcespace_import//a; type=integer; label=User have to be logged in in db to use this service
		backendLoginIsMandatory = 0

		# cat=plugin.tx_rkwresourcespace_import//a; type=integer; label=Shows a form for user to upload an image by it's resource space uid
		enableFormUpload = 0

		# resourceSpace API data
		resourceSpaceApi {
			# cat=plugin.tx_rkwresourcespace_import//a; type=string; label=BaseUrl of API
			baseUrl =

			# cat=plugin.tx_rkwresourcespace_import//a; type=string; label=API user name
			user =

			# cat=plugin.tx_rkwresourcespace_import//a; type=string; label=API password
			password =

			# cat=plugin.tx_rkwresourcespace_import//a; type=string; label=API private key
			privateKey =

            # cat=plugin.tx_rkwresourcespace_import//a; type=string; label=Proxy
            proxy =

            # cat=plugin.tx_rkwresourcespace_import//a; type=string; label=Proxy Username
            proxyUsername =

            # cat=plugin.tx_rkwresourcespace_import//a; type=string; label=Proxy Password
            proxyPassword =
		}
		# upload specifications
		upload {
			# cat=plugin.tx_rkwresourcespace_import//a; type=integer; label=uid of storage (table: sys_file_storage)
			sysFileStorageUid = 1

			# image specifications
			processing = 0
			processing {
				# cat=plugin.tx_rkwresourcespace//f; type=string; label=force file extensions (e.g. jpg)
				forceFormat =

				# cat=plugin.tx_rkwresourcespace//f; type=string; label=max width for images
				maxWidth =
			}
		}
	}
}
