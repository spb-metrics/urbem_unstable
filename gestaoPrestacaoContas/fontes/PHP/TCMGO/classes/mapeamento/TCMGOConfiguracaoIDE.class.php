<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision:$
    $Name$
    $Author:$
    $Date:$

*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCMGOConfiguracaoIDE extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TCMGOConfiguracaoIDE()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.configuracao_ide");

        $this->setCampoCod('cod_entidade');
        $this->setComplementoChave('exercicio');

        $this->AddCampo( 'cod_entidade'         ,'integer' ,true, ''  ,true ,true  );
        $this->AddCampo( 'exercicio'            ,'varchar' ,true, '4' ,true ,true  );
        $this->AddCampo( 'cgm_chefe_governo'    ,'integer' ,true, ''  ,false,true  );
        $this->AddCampo( 'cgm_contador'         ,'integer' ,true, ''  ,false,true  );
        $this->AddCampo( 'cgm_controle_interno' ,'integer' ,true, ''  ,false,true  );
        $this->AddCampo( 'crc_contador'                  ,'integer' ,true, ''  ,false,false );
        $this->AddCampo( 'uf_crc_contador'                ,'integer' ,true, ''  ,false,true  );
    }
}
