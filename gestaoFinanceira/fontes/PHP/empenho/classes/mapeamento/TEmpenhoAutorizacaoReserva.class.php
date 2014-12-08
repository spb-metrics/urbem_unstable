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
  * Classe de mapeamento da tabela EMPENHO.AUTORIZACAO_RESERVA
  * Data de Criação: 04/12/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
  *Casos de uso: uc-02.03.02

*/

/*
$Log$
Revision 1.6  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.AUTORIZACAO_RESERVA
  * Data de Criação: 04/12/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoAutorizacaoReserva extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoAutorizacaoReserva()
{
    parent::Persistente();
    $this->setTabela('empenho.autorizacao_reserva');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_entidade,exercicio,cod_autorizacao');

    $this->AddCampo('cod_entidade'      ,'integer',true,'',true,true);
    $this->AddCampo('exercicio'         ,'varchar',true,'4',true,true);
    $this->AddCampo('cod_autorizacao'   ,'integer',true,'',true,true);
    $this->AddCampo('cod_reserva'       ,'integer',true,'',true,true);

}
}
