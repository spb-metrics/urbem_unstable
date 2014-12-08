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
    * Classe de mapeamento da tabela compras.ordem_item
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 25603 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-24 10:58:59 -0300 (Seg, 24 Set 2007) $

    * Casos de uso: uc-03.04424
*/

/*
$Log$
Revision 1.5  2007/09/24 13:58:59  hboaventura
Correção do caso de uso

Revision 1.4  2006/11/07 16:41:27  larocca
Inclusão dos Casos de Uso

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.ordem_item
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasOrdemItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasOrdemItem()
{
    parent::Persistente();
    $this->setTabela("compras.ordem_item");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_ordem,cod_pre_empenho,num_item,tipo,exercicio_pre_empenho');

    $this->AddCampo('exercicio'             ,'char',true,'4',true,true);
    $this->AddCampo('cod_entidade'          ,'integer',true,'',true,true);
    $this->AddCampo('cod_ordem'             ,'integer',true,'',true,true);
    $this->AddCampo('cod_pre_empenho'       ,'integer',true,'',true,true);
    $this->AddCampo('num_item'              ,'integer',true,'',true,true);
    $this->AddCampo('quantidade'            ,'numeric',true,'14,4',false,false);
    $this->AddCampo('vl_total'              ,'numeric',true,'14,2',false,false);
    $this->AddCampo('tipo'                  ,'char',true,'4',true,true);
    $this->AddCampo('exercicio_pre_empenho' ,'char',true,'4',true,true);

}
}