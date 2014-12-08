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
    * Extensão da Classe de mapeamento
    * Data de Criação: 05/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 09:58:01 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:20:03  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/09/11 03:14:16  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 08/09/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBATipoCertidao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBATipoCertidao()
{
    parent::Persistente();
    $this->setTabela('tcmba.tipo_certidao');

    $this->setCampoCod('cod_tipo_tcm');

    $this->AddCampo('cod_tipo_tcm','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',false,'200',false,false);
    $this->AddCampo('cod_tipo','integer',false,'',true,'TLicitacaoDocumento','cod_documento');
}

function montaRecuperaRelacionamento()
{
    $stSql .= " SELECT  *                                       \n";
    $stSql .= " FROM    ".$this->getTabela()." as tab           \n";
    $stSql .= "         LEFT JOIN licitacao.documento as lici   \n";
    $stSql .= "         ON ( tab.cod_tipo = lici.cod_documento )\n";
    $stSql .= " ORDER BY tab.cod_tipo_tcm                       \n";

    return $stSql;
}

}
