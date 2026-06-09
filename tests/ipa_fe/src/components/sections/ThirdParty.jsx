import { useLayoutEffect } from 'react'
import { runThirdParty } from '../../helpers/thirdparty'

function ThirdParty() {
  useLayoutEffect(() => {
    runThirdParty()
  }, [])

  // return <script type="text/javascript">runThirdParty</script>
}

export default ThirdParty
