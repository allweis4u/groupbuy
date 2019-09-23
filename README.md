branch
    keep master (git default branch) in stable (master branch is release indicator)
    feature branch (fb): workspace for development or hot fix
        fb naming: fb##-feature_name

tree structure
RegTech_SmartRCM\
    component\
        module_1\
            src\    <= source code
            test\   <= unit test code
            build.sh   <= build script for the module
        module_2\ <= same folders as module_1
        module_...\ <= same folders as module_1
    integration_test\ <= for system integration test

principle for build script
    should take two parameters, Version and Release, for package naming
    create the binary files in the same folder with build.sh
    package file naming rule
        $component-$submodule-$Version-$Release.x86_64.*
        ex. RWC-1.0-12345.x86_64.exe
        ex. RSA-1.0-10001.x86_64.jar
        thus, user can get version information not only from package metadata but also from the file name
        it’s up to component if any special cases and needs

operation policy
    1. create new branch by feature/bug owner
    2. code review and verified fb when finished
    3. apply merge request to master owner

.gitmessage Template
'=======================================
Reason: 
Author: 
IssueNo:
Reviewer:
'=======================================

TO BE NOTICED: DON'T COMMIT DISTRIBUTION FILES INTO GIT (https://github.com/rfrail3/tuptime/issues/11)
    commit source-less binary files in git might caused .git history and index tree grow abnormally, and long waiting when checkout it
