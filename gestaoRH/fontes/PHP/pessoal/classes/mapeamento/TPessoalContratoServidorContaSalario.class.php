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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_CONTA_SALARIO
  * Data de Criação: 17/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_CONTA_SALARIO
  * Data de Criação: 17/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorContaSalario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorContaSalario()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_conta_salario');

    $this->setCampoCod('cod_contrato');
    $this->setComplementoChave('');

    $this->AddCampo('cod_contrato'  ,'integer',true,'',true,false);
    $this->AddCampo('cod_agencia'   ,'integer',true,'',true,false);
    $this->AddCampo('cod_banco'     ,'integer',true,'',true,false);
    $this->AddCampo('nr_conta'      ,'varchar',true,'5.2',false,false);

}

}
